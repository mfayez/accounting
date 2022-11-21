<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

use CasperBiering\Dotnet\BinaryXml\Decoder;
use CasperBiering\Dotnet\BinaryXml\SoapDecoder;
use CasperBiering\Dotnet\BinaryXml\Encoder;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Address;
use App\Models\ETAItem;
use App\Models\ETAInvoice;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\TaxableItem;
use App\Models\TaxTotal;
use App\Models\Value;
use App\Models\Discount;
use App\Models\Receiver;
use App\Models\Issuer;
use App\Models\Upload;
use App\Models\Settings;
use App\Models\SBItemMap;
use App\Models\SBBranchMap;

use App\Http\Traits\SalesBuzzAuthenticator;
use App\Http\Traits\ExcelWrapper;

class SalesBuzzController extends Controller
{
    use SalesBuzzAuthenticator;
	use ExcelWrapper;
    
    public function syncSalesOrders(Request $request)
    {
		$data = $request->validate([
			"username" 				=> "required|string",
			"password" 				=> "required|string",
			"buid" 					=> "required|string",
			"value" 				=> "required|integer",
			"period"				=> "required|integer",
			'issuer'				=> 'required',
			'taxpayerActivityCode'	=> 'required',
						
		]);

		$branchMap = SBBranchMap::where('branch_id', $data['issuer']['Id'])->first();
		if (!isset($branchMap)){
			return [
				'code'      =>  404,
				'message'   =>  "SalesBuzz Configuration is incomplete! Please contact system support."
			];
		}
		$url = $branchMap->sb_url;
		
		$this->AuthenticateSB($request, $data['username'], $data['password'], $data['buid'], $url);
		if ($this->salezbuzz_cookies == ""){
			return [
				'code'      =>  404,
				'message'   =>  "SalesBuzz Authentication Failed"
			];
			
		}

		$pageSize = 10;
		$skip = ($data['value'] - 1) * $pageSize;
        //get orders now
		
        $url = "$url/ClientBin/BI-SalesBuzz-BackOffice-Web-Services-PromotionHeaderDS.svc/binary/GetAR_Order?StopLoading=false&\$skip=$skip&\$take=$pageSize&\$includeTotalCount=True";
		if ($skip == 0)
			$url = "$url/ClientBin/BI-SalesBuzz-BackOffice-Web-Services-PromotionHeaderDS.svc/binary/GetAR_Order?StopLoading=false&\$take=$pageSize&\$includeTotalCount=True";	
		/*$timestamp = Carbon::now()
						->add(1969, 'year')
						->sub($data['period'], 'day')
						->timestamp;
		$timestamp = $timestamp * 10000000;
		*/
		
		$decoder = new Decoder();
		$response = Http::withHeaders($this->salezbuzz_headers)
                    ->get($url);
		$xmldata = $decoder->decode($response->body());
		$xmldata = preg_replace('~(</?|\s)([a-z0-9_]+):~is', '$1$2_', $xmldata);
		
		$simpleXml = simplexml_load_string($xmldata);
		$sb_data = $this->xmlToArray($simpleXml);
		
		$activity = $data['taxpayerActivityCode']['code'];
		$issuer = $data['issuer']['Id'];

		$date1 = Carbon::now();
		$lastInv = null;
		foreach($sb_data["GetAR_OrderResponse"]["GetAR_OrderResult"]["a_RootResults"]["b_AR_Order"] as $invoice) {
			if ($invoice['b_OrderStatus'] != 4)
				continue;
				
			$invoice_lines = collect($sb_data["GetAR_OrderResponse"]["GetAR_OrderResult"]["a_IncludedResults"]["b_anyType"])
							->where("c_OrderID", $invoice["b_OrderID"])
							->where("@i_type", "c:AR_OrderLines");
			$invoice2 = Invoice::firstWhere(['internalID' => $invoice['b_OrderID']]);
			if (!$invoice2)// && $invoice2->status != "Valid") //check else part
			{
				//if ($invoice['CompleteOrderReverse'] == false){
					//remove all invoice attributes, and re-create
					/*foreach($invoice2->invoiceLines as $line) {
						$line->discount()->delete();
						$line->taxableItems()->delete();
						$line->delete();
						$line->unitValue()->delete();
					}
					$invoice2->taxTotals()->delete();
					$invoice2->delete();*/
					$invoice2 = $this->AddMissingInvoice($invoice, $invoice_lines, $issuer, $activity);
				//}
				//else
				//{
					//$old_inv = Invoice::firstWhere(['internalID' => $invoice['RefOrder']]);
					//if ($old_inv)
					//	$invoice2 = $this->ReverseInvoice($old_inv, $invoice);
				//}
			}
			$lastInv = $invoice["b_OrderID"];
			if ($invoice2)
				if ($invoice2->dateTimeIssued < $date1)
					$date1 = $invoice2->dateTimeIssued;
		}

		return ["totalPages" => min($data['value'] + 1, 100),
				"currentPage" => $data['value'],
				"lastDate" => $date1,
				"lastInvoice" => $lastInv
			];	
	}
	
	private function AddMissingInvoice($sb_invoice, $sb_invoice_lines, $issuer, $activity)
	{
		$invoice = new Invoice();
		$receiver = Receiver::firstWhere(['code' => $sb_invoice['b_CustomerNo']]);
		
		if (!$receiver) {
			$item2 = new Address();
			$item2->country = "EG";
			$item2->governate = "Cairo";
			$item2->regionCity = is_array($sb_invoice['b_DeliveryAddress']) ? "N/A" : $sb_invoice['b_DeliveryAddress'];
			$item2->street = is_array($sb_invoice['b_DeliveryAddress']) ? "N/A" : $sb_invoice['b_DeliveryAddress'];
			$item2->buildingNumber = "1";
			$item2->postalCode = "12345";
			$item2->save();
			$item = new Receiver();
			$item->code = $sb_invoice['b_CustomerNo'];
			$item->name = $sb_invoice['b_CustomerNameA'];
			$item->receiver_id = "0";
			$item->type = "P";
			$item2->receiver()->save($item);
			$receiver = $item;
		}
		
		$invoice->issuer_id = $issuer;
		$invoice->receiver_id = $receiver->Id;
		$invoice->statusreason = "تحميل الفاتورة من SalesBuzz";
		$invoice->documentType = is_array($sb_invoice['b_ReturnReason']) ? "I" : "C";
		$invoice->documentTypeVersion = SETTINGS_VAL('application settings', 'invoiceVersion', '1.0');;
		$invoice->totalDiscountAmount = 0;
		$invoice->totalSalesAmount = 0;
		$invoice->netAmount = 0;
		$invoice->totalAmount = 0;
		$invoice->extraDiscountAmount = 0;
		$invoice->totalItemsDiscountAmount = 0;
		$invoice->status = "In Review";	
		$invoice->internalID = $sb_invoice['b_OrderID'];
		if (strlen($sb_invoice['b_ConfirmDate']) > 15)
			$invoice->dateTimeIssued = Carbon::createFromDate($sb_invoice['b_ConfirmDate']);
		else
			$invoice->dateTimeIssued = Carbon::now()->toDateString();
		$invoice->taxpayerActivityCode = $activity;
		$invoice->save();

		foreach($sb_invoice_lines as $line) {
			if (!isset($line['c_ItemID']))
				continue;
			$mapItem = SBItemMap::find($line['c_ItemID']);
			if (!$mapItem) {
				$mapItem = new SBItemMap();
				$mapItem->SBCode = $line['c_ItemID'];
				$mapItem->ItemNameA = $line['c_ItemNameA'];
				$mapItem->ItemNameE = $line['c_ItemNameE'];
				$mapItem->save();
				continue;
			}
			//if there is not map for this item ignore it
			if (is_null($mapItem->ETACode))
				continue;
			if ($line['c_Qty'] == 0)
				continue;
				
			$unitValue = new Value(['currencySold' => "EGP", 
				'amountEGP' => $line['c_UnitPrice'] < 0 ? -$line['c_UnitPrice'] : $line['c_UnitPrice'],
			]);
			$unitValue->save();
			$invoiceline = new InvoiceLine((array)$line);
			$invoiceline->description = $line['c_ItemNameA'];
			$invoiceline->itemType = "GS1";
			$invoiceline->itemCode = $mapItem->ETACode;
			if ($line["c_UOM"] == "CTN")
				$invoiceline->unitType = "CT";
			else
				$invoiceline->unitType = "EA";
			$invoiceline->quantity = $line['c_Qty'] < 0 ? -$line['c_Qty'] : $line['c_Qty'];
			$invoiceline->internalCode = $line['c_ItemID'];
			$invoiceline->salesTotal = $line['c_LineTotal'] < 0 ? -$line['c_LineTotal'] : $line['c_LineTotal'];	//done
			$invoiceline->netTotal = $line['c_LineTotal'] < 0 ? -$line['c_LineTotal'] : $line['c_LineTotal'];	//done
			$invoiceline->itemsDiscount = $line["c_PromotionsTotal"] < 0 ? -$line["c_PromotionsTotal"] : $line["c_PromotionsTotal"]; //done
			$invoiceline->total = $invoiceline->netTotal - $invoiceline->itemsDiscount;	//done
			if ($invoiceline->total < 0)
			{
				$invoiceline->total = 0;
				$invoiceline->itemsDiscount = -$invoiceline->netTotal;
			}
			$invoiceline->valueDifference = 0;
			$invoiceline->totalTaxableFees = 0;
			$invoiceline->invoice_id = $invoice->Id;
			$invoiceline->unitValue_id = $unitValue->Id;
			$invoiceline->save();

            /*foreach($line->taxableItems as $taxitem) {
				$item = new TaxableItem((array)$taxitem);
				$item->invoiceline_id = $invoiceline->Id;
				$item->save();
			}*/
		}
		/*
		foreach($document->taxTotals as $totalTax) {
			$taxTotal = new TaxTotal((array)$totalTax);
			$taxTotal->invoice_id = $invoice->Id;
			$taxTotal->save();
		}*/

		$invoice->normalize();
		$invoice->save();
		return $invoice;
	}

	//not used and was programmed for the old version of SalesBuzz (json)
	private function ReverseInvoice($old_inv, $sb_invocie)
	{
		$new_inv = $old_inv->replicate()
							->fill(['internalID' => $sb_invocie['OrderID'],
									'documentType' => 'C',
									'statusreason' => "تحميل الفاتورة من SalesBuzz",
		]);
		$new_inv->save();
		foreach($old_inv->invoiceLines as $inv_line){
			$unitValue = $inv_line->unitValue->replicate();
			$unitValue->save();
			$inv_line->replicate()
					 ->fill(['invoice_id' => $new_inv->Id,
					 		 'unitValue_id' => $unitValue->Id,
							 'status' => 'In Review'
					 ])->save();
		}
		return $new_inv;
	}

	public function UploadItemsMap(Request $request)
	{
		$temp = [];
		$extension = $request->file->extension();
		if ($extension == 'xlsx' || $extension == 'xls')
			$temp = $this->xlsxToArray($request->file, $extension);
		else if ($extension == 'csv')
			$temp = $this->csvToArray($request->file);
		else
			return json_encode(["Error" => true, "Message" => __("Unsupported File Type!")]);

		foreach	($temp as $map)
		{
			SBItemMap::updateOrCreate(
				['SBCode' => $map['SBCode']],
				['SBCode' => $map['SBCode'],
				 'ETACode' => $map['ETACode'], 
				 'ItemNameA' => $map['ItemNameA'],
				 'ItemNameE' => $map['ItemNameE']
				]
			);
			
		}
	}

	public function indexItemsMap(Request $request)
	{
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('SBCode', 'LIKE', "%{$value}%")
					->orWhere('ETACode', 'LIKE', "%{$value}%")
					->orWhere('ItemNameA', 'LIKE', "%{$value}%")
					->orWhere('ItemNameE', 'LIKE', "%{$value}%");
            });
        });

        $items = QueryBuilder::for(SBItemMap::class)
			->defaultSort('SBCode')
            ->allowedSorts(['SBCode', 'ETACode', 'ItemNameA', 'ItemNameE'])
            ->allowedFilters(['SBCode', 'ETACode', 'ItemNameA', 'ItemNameE', $globalSearch])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('SalesBuzz/IndexItemsMap', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addColumns([
                'SBCode' 	=> __('SalesBuzz Code'),
                'ETACode' 	=> __('ETA Code'),
                'ItemNameA' => __('Arabic Name'),
				'ItemNameE' => __('English Name')
            ]);
        });
	}

	public function updateItem(Request $request)
	{
		$map = $request->validate([
			'SBCode' => 'required',
			'ETACode' => 'required',
			'ItemNameA' => 'required',
			'ItemNameE' => 'required',
		]);
		SBItemMap::updateOrCreate(
			['SBCode' => $map['SBCode']],
			['SBCode' => $map['SBCode'],
			 'ETACode' => $map['ETACode'], 
			 'ItemNameA' => $map['ItemNameA'],
			 'ItemNameE' => $map['ItemNameE']
			]
		);
	}

	public function deleteItem(Request $request)
	{
		$map = $request->validate([
			'SBCode' => 'required'
		]);
		SBItemMap::findOrFail($map['SBCode'])->delete();
	}

	public function indexBranchesMap(Request $request)
	{
		$data = DB::select("SELECT t1.Id as BID, t1.Name as BName, t2.sb_url as SBUrl
			from Issuer t1 left outer join
				sb_branches_map t2 on t1.Id = t2.branch_id"
		);

		return Inertia::render('SalesBuzz/IndexBranchesMap', [
            'items' => $data,
        ]);
    }

	public function updateBranchesMap(Request $request){
		$data = $request->validate([
			'BID' => 'required',
			'SBUrl' => 'required'
		]);

		$branch_map = SBBranchMap::updateOrCreate(
			['branch_id' => $data['BID']],
			['branch_id' => $data['BID'],
			 'sb_url' => $data['SBUrl']
			]
		);

	}

	public function XML2JSON($xml) {
		function normalizeSimpleXML($obj, &$result) {
			$data = $obj;
			if (is_object($data)) {
				$data = get_object_vars($data);
			}
			if (is_array($data)) {
				foreach ($data as $key => $value) {
					$res = null;
					normalizeSimpleXML($value, $res);
					if (($key == '@attributes') && ($key)) {
						$result = $res;
					} else {
						$result[$key] = $res;
					}
				}
			} else {
				$result = $data;
			}
		}
		normalizeSimpleXML(simplexml_load_string($xml), $result);
		return json_encode($result);
	}

	public function xmlToArray($xml, $options = array()) {
		$defaults = array(
			'namespaceRecursive' => false,  //setting to true will get xml doc namespaces recursively
			'removeNamespace' => false,     //set to true if you want to remove the namespace from resulting keys (recommend setting namespaceSeparator = '' when this is set to true)
			'namespaceSeparator' => ':',    //you may want this to be something other than a colon
			'attributePrefix' => '@',       //to distinguish between attributes and nodes with the same name
			'alwaysArray' => array(),       //array of xml tag names which should always become arrays
			'autoArray' => true,            //only create arrays for tags which appear more than once
			'textContent' => '$',           //key used for the text content of elements
			'autoText' => true,             //skip textContent key if node has no attributes or child nodes
			'keySearch' => false,           //optional search and replace on tag and attribute names
			'keyReplace' => false           //replace values for above search values (as passed to str_replace())
		);
		$options = array_merge($defaults, $options);
		$namespaces = $xml->getDocNamespaces($options['namespaceRecursive']);
		$namespaces[''] = null; //add base (empty) namespace
	 
		//get attributes from all namespaces
		$attributesArray = array();
		foreach ($namespaces as $prefix => $namespace) {
			if ($options['removeNamespace']) {
				$prefix = '';
			}
			foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
				//replace characters in attribute name
				if ($options['keySearch']) {
					$attributeName =
						str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
				}
				$attributeKey = $options['attributePrefix']
					. ($prefix ? $prefix . $options['namespaceSeparator'] : '')
					. $attributeName;
				$attributesArray[$attributeKey] = (string)$attribute;
			}
		}
	 
		//get child nodes from all namespaces
		$tagsArray = array();
		foreach ($namespaces as $prefix => $namespace) {
			if ($options['removeNamespace']) {
				$prefix = '';
			}
	
			foreach ($xml->children($namespace) as $childXml) {
				//recurse into child nodes
				$childArray = $this->xmlToArray($childXml, $options);
				$childTagName = key($childArray);
				$childProperties = current($childArray);
	 
				//replace characters in tag name
				if ($options['keySearch']) {
					$childTagName =
						str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
				}
	
				//add namespace prefix, if any
				if ($prefix) {
					$childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
				}
	 
				if (!isset($tagsArray[$childTagName])) {
					//only entry with this key
					//test if tags of this type should always be arrays, no matter the element count
					$tagsArray[$childTagName] =
							in_array($childTagName, $options['alwaysArray'], true) || !$options['autoArray']
							? array($childProperties) : $childProperties;
				} elseif (
					is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
					=== range(0, count($tagsArray[$childTagName]) - 1)
				) {
					//key already exists and is integer indexed array
					$tagsArray[$childTagName][] = $childProperties;
				} else {
					//key exists so convert to integer indexed array with previous value in position 0
					$tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
				}
			}
		}
	 
		//get text content of node
		$textContentArray = array();
		$plainText = trim((string)$xml);
		if ($plainText !== '') {
			$textContentArray[$options['textContent']] = $plainText;
		}
	 
		//stick it all together
		$propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
			? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;
	 
		//return node as array
		return array(
			$xml->getName() => $propertiesArray
		);
	}
}


