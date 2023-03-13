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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ETA\Address;
use App\Models\ETAItem;
use App\Models\ETAInvoice;
use App\Models\ETA\Invoice;
use App\Models\ETA\InvoiceLine;
use App\Models\ETA\TaxableItem;
use App\Models\ETA\TaxTotal;
use App\Models\ETA\Value;
use App\Models\Discount;
use App\Models\ETA\Receiver;
use App\Models\ETA\Issuer;
use App\Models\General\Upload;
use App\Models\General\Settings;
use App\Models\SBItemMap;
use App\Models\SBBranchMap;
use App\Models\SBItemTax;

use App\Http\Traits\SalesBuzzAuthenticator;
use App\Http\Traits\ExcelWrapper;
use App\Http\Traits\XMLWrapper;

class SalesBuzzController extends Controller
{
    use SalesBuzzAuthenticator;
	use ExcelWrapper;
	use XMLWrapper;
    
	public function UploadInvoice(Request $request)
	{
		$data = $request->validate([
			'issuer'				=> 'required',
			'taxpayerActivityCode'	=> 'required',
			'data'					=> 'required',
		]);
		//convert data from base64 to binary->then xml
		$decoder = new Decoder();
		$data2 = $decoder->decode(base64_decode($data['data']));
		return $this->processSBInvoices($data2, $data['issuer'], $data['taxpayerActivityCode'], 1);
	}

	public function UploadSettelment(Request $request)
	{
		$data = $request->validate([
			'invoice_id'	=> 'required',
			'data'			=> 'required',
		]);
		//convert data from base64 to binary->then xml
		$decoder = new Decoder();
		$data2 = $decoder->decode(base64_decode($data['data']));
		$this->processSBSettelment($data2, $data['invoice_id']);
		return ["Message" => "Success"];
	}

    public function syncSalesOrders(Request $request)
    {
		$data = $request->validate([
			"username" 				=> "required|string",
			"password" 				=> "required|string",
			"value" 				=> "required|integer",
			"period"				=> "required|integer",
			'issuer'				=> 'required',
			'taxpayerActivityCode'	=> 'required',
			'tax_inverse'			=> 'boolean',
			'settled_transactions'	=> 'boolean',
						
		]);

		$branchMap = SBBranchMap::where('branch_id', $data['issuer']['Id'])->first();
		if (!isset($branchMap)){
			return [
				'code'      =>  404,
				'message'   =>  "SalesBuzz Configuration is incomplete! Please contact system support."
			];
		}
		$url_base = $branchMap->sb_url;
		$buid = $branchMap->sb_bu;
		
		$this->AuthenticateSB($request, $data['username'], $data['password'], $buid, $url_base);
		if ($this->salezbuzz_cookies == ""){
			return [
				'code'      =>  404,
				'message'   =>  "SalesBuzz Authentication Failed"
			];
			
		}

		$pageSize = 10;
		$skip = ($data['value'] - 1) * $pageSize;
        //get orders now
		
        $url = "$url_base/ClientBin/BI-SalesBuzz-BackOffice-Web-Services-PromotionHeaderDS.svc/binary/GetAR_Order?StopLoading=false&\$skip=$skip&\$take=$pageSize&\$includeTotalCount=True";
		if ($skip == 0)
			$url = "$url_base/ClientBin/BI-SalesBuzz-BackOffice-Web-Services-PromotionHeaderDS.svc/binary/GetAR_Order?StopLoading=false&\$take=$pageSize&\$includeTotalCount=True";	
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
		$retVal = $this->processSBInvoices($xmldata, $data['issuer']['Id'],
			$data['taxpayerActivityCode']['code'],
			$data['value']
			//$data['tax_inverse']
		);

		if ($data['settled_transactions'])
		{
			foreach (json_decode($retVal['lstInvoices']) as $invID){
				$url = "$url_base/ClientBin/BI-SalesBuzz-BackOffice-Web-Services-PromotionHeaderDS.svc/binary/GetSettledTransactions?TransRefID=$invID&TransType=85&\$take=100";
				$decoder = new Decoder();
				$response = Http::withHeaders($this->salezbuzz_headers)
							->get($url);
				$xmldata11 = $decoder->decode($response->body());
				$this->processSBSettelment($xmldata11, $invID);
			}
		}

		return $retVal;
	}
	
	public function processSBSettelment($xmldata2, $invID)
	{
		$xmldata = preg_replace('~(</?|\s)([a-z0-9_]+):~is', '$1', $xmldata2);
		$simpleXml = simplexml_load_string($xmldata);
		$sb_data2 = $this->xmlToArray($simpleXml);
		$totalDiscount = 0;
		if (array_key_exists("GetSettledTransactionsResponse", $sb_data2) && 
			array_key_exists("GetSettledTransactionsResult", $sb_data2["GetSettledTransactionsResponse"]) && 
			array_key_exists("IncludedResults", $sb_data2["GetSettledTransactionsResponse"]["GetSettledTransactionsResult"]) && 
			array_key_exists("anyType", $sb_data2["GetSettledTransactionsResponse"]["GetSettledTransactionsResult"]["IncludedResults"]))
		{
			$array = $sb_data2["GetSettledTransactionsResponse"]["GetSettledTransactionsResult"]["IncludedResults"]["anyType"];
			#it has only one element so just process it as single element not an array.
            if (count(array_filter(array_keys($array), 'is_string')) > 0)
            {
				$setteldTrans = $array;
				if (is_array($setteldTrans) && array_key_exists("@type", $setteldTrans))
				{
					if ($setteldTrans["@type"] == "c:AR_CustTrans" && $setteldTrans["TransRefLineID"] > 0)
						$totalDiscount += $setteldTrans["SettledAmount"];
				}
            }
            else
			{
				foreach($array as $setteldTrans)
				{
					if (is_array($setteldTrans) && array_key_exists("@type", $setteldTrans))
					{
						if ($setteldTrans["@type"] == "c:AR_CustTrans" && $setteldTrans["TransRefLineID"] > 0)
							$totalDiscount += $setteldTrans["SettledAmount"];
					}
				}
			}
		}
		$totalDiscount = abs($totalDiscount);
		if ($totalDiscount > 0){
			$invoice = Invoice::where('internalID', $invID)->first();
			if (isset($invoice)){
				$invoice->extraDiscountAmount = $totalDiscount;
				$invoice->totalAmount = $invoice->totalAmount - $totalDiscount;
				$invoice->save();
			}
		}
	}
	public function processSBInvoices($xmldata2, $issuer2, $taxpayerActivityCode2, $page, $tax_inverse = false)
	{
		$xmldata = preg_replace('~(</?|\s)([a-z0-9_]+):~is', '$1', $xmldata2);
		$simpleXml = simplexml_load_string($xmldata);
		$sb_data = $this->xmlToArray($simpleXml);
		
		$activity = $taxpayerActivityCode2;
		$issuer = $issuer2;

		$date1 = Carbon::now();
		$lstOfInvoices = [];
		$lastInv = null;
		foreach($sb_data["GetAR_OrderResponse"]["GetAR_OrderResult"]["RootResults"]["AR_Order"] as $invoice) {
			if ($invoice['OrderStatus'] != 4)
				continue;
				
			$invoice_lines = collect($sb_data["GetAR_OrderResponse"]["GetAR_OrderResult"]["IncludedResults"]["anyType"])
							->where("OrderID", $invoice["OrderID"])
							->where("@type", "c:AR_OrderLines");
			$invoice2 = Invoice::firstWhere(['internalID' => $invoice['OrderID']]);
			if ($invoice2 && $invoice2->status == 'In Review')
			{
				foreach($invoice2->invoiceLines as $line) {
					$line->taxableItems()->delete();
					$line->delete();
					$line->unitValue()->delete();
					$line->discount()->delete();
				}
				$invoice2->taxTotals()->delete();
				$invoice2->delete();
				$invoice2 = null;
			}
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
					$invoice2 = $this->AddMissingInvoice($invoice, $invoice_lines, $issuer, $activity, $tax_inverse);
					#add $invoice2 to $lstOfInvoices
					array_push($lstOfInvoices, $invoice2->internalID);
				//}
				//else
				//{
					//$old_inv = Invoice::firstWhere(['internalID' => $invoice['RefOrder']]);
					//if ($old_inv)
					//	$invoice2 = $this->ReverseInvoice($old_inv, $invoice);
				//}
			}
			$lastInv = $invoice["OrderID"];
			if ($invoice2)
				if ($invoice2->dateTimeIssued < $date1)
					$date1 = $invoice2->dateTimeIssued;
		}

		return ["totalPages" => min($page + 1, 100),
				"currentPage" => $page,
				"lastDate" => $date1,
				"lstInvoices" => json_encode($lstOfInvoices),
				"lastInvoice" => $lastInv
			];	
	}
	
	private function AddMissingInvoice($sb_invoice, $sb_invoice_lines, $issuer, $activity, $tax_inverse)
	{
		$invoice = new Invoice();
		$receiver = Receiver::firstWhere(['code' => $sb_invoice['CustomerNo']]);
		
		if (!$receiver) {
			$item2 = new Address();
			$item2->country = "EG";
			$item2->governate = "Cairo";
			$item2->regionCity = is_array($sb_invoice['DeliveryAddress']) ? "N/A" : $sb_invoice['DeliveryAddress'];
			$item2->street = is_array($sb_invoice['DeliveryAddress']) ? "N/A" : $sb_invoice['DeliveryAddress'];
			$item2->buildingNumber = "1";
			$item2->postalCode = "12345";
			$item2->save();
			$item = new Receiver();
			$item->code = $sb_invoice['CustomerNo'];
			$item->name = $sb_invoice['CustomerNameA'];
			$item->receiver_id = "0";
			$item->type = "P";
			$item2->receiver()->save($item);
			$receiver = $item;
		}
		
		$invoice->issuer_id = $issuer;
		$invoice->receiver_id = $receiver->Id;
		$invoice->statusreason = "تحميل الفاتورة من SalesBuzz";
		//TODO document type may be 'C' if document kind is 'مردودات'
		$invoice->documentType = is_array($sb_invoice['ReturnReason']) ? "I" : "C";
		$invoice->documentTypeVersion = SETTINGS_VAL('application settings', 'invoiceVersion', '1.0');;
		$invoice->totalDiscountAmount = 0; //before tax calculations ($line->discount())
		$invoice->totalSalesAmount = 0;
		$invoice->netAmount = 0;
		$invoice->totalAmount = 0;
		$invoice->extraDiscountAmount = 0;
		$invoice->totalItemsDiscountAmount = 0; //after tax is calculated ($line->itemsDiscount)
		$invoice->status = "In Review";	
		$invoice->internalID = $sb_invoice['OrderID'];
		if (is_string($sb_invoice['ConfirmDate']) && strlen($sb_invoice['ConfirmDate']) > 15)
			$invoice->dateTimeIssued = Carbon::createFromDate($sb_invoice['InvoiceDate']);
		else
			$invoice->dateTimeIssued = Carbon::now()->toDateString();
		$invoice->taxpayerActivityCode = $activity;
		$invoice->save();

		foreach($sb_invoice_lines as $line) {
			if (!isset($line['ItemID']))
				continue;
			$mapItem = SBItemMap::find($line['ItemID']);
			if (!$mapItem) {
				$mapItem = new SBItemMap();
				$mapItem->SBCode = $line['ItemID'];
				$mapItem->ItemNameA = $line['ItemNameA'];
				$mapItem->ItemNameE = $line['ItemNameE'];
				$mapItem->save();
				continue;
			}
			//if there is not map for this item ignore it
			if (is_null($mapItem->ETACode))
				continue;
			if ($line['Qty'] == 0)
				continue;

			//if the tax amount is not related to tax type add it to the unit price
			$extraUnitValue = 0;	
			if (is_string($line["TaxID"]) && ($line["TaxID"] != "Tax14" && 
											  $line["TaxID"] != "14%" && 
											  $line["TaxID"] != "14N" && 
											  $line["TaxID"] != "14"))
				$extraUnitValue = abs(floatval($line["TaxesTotal"])) / $line['Qty'];
			$unitValue = new Value(['currencySold' => "EGP", 
				#'amountEGP' => round(($line['UnitPrice'] < 0 ? -$line['UnitPrice'] : $line['UnitPrice'] + 0.004), 2),
				'amountEGP' => $extraUnitValue + ( $line['UnitPrice'] < 0 ? -$line['UnitPrice'] : $line['UnitPrice'] )
			]);
			$unitValue->save();
			
			$invoiceline = new InvoiceLine((array)$line);
			$invoiceline->description = $line['ItemNameA'];
			$invoiceline->itemType = "GS1";
			$invoiceline->itemCode = $mapItem->ETACode;
			if ($line["UOM"] == "CTN")
				$invoiceline->unitType = "CT";
			else
				$invoiceline->unitType = "EA";
			$invoiceline->quantity = $line['Qty'] < 0 ? -$line['Qty'] : $line['Qty'];
			$invoiceline->internalCode = $line['ItemID'];
			#$invoiceline->salesTotal = round(($line['LineCost'] < 0 ? -$line['LineCost'] : $line['LineCost'])+0.004, 2);	//done
			#$invoiceline->netTotal = round(($line['LineCost'] < 0 ? -$line['LineCost'] : $line['LineCost']) + 0.004, 2);	//done
			$invoiceline->salesTotal = $unitValue->amountEGP * $invoiceline->quantity;
			//$invoiceline->netTotal = $unitValue->amountEGP * $invoiceline->quantity;
			$discountBeforeTax = $line["PromotionsTotal"] < 0 ? -$line["PromotionsTotal"] : $line["PromotionsTotal"]; //done
			$discountAfterTax = 0;
			//sometimes c_PromotionsTotal is zero despite there is a discount
			//we can use c_LineTotal to populate $invoiceline->netTotal and recalcualte the discount
			$invoiceline->total = $invoiceline->netTotal = $invoiceline->salesTotal - $discountBeforeTax;	//done
			if ($invoiceline->total < 0)
			{
				$invoiceline->total = 0;
				$invoiceline->netTotal = 0;
				$discountBeforeTax = $invoiceline->salesTotal;
			}
			$invoiceline->valueDifference = 0;//$mapItem->Val_Diff * $invoiceline->quantity;
			$invoiceline->totalTaxableFees = 0;
			$invoiceline->invoice_id = $invoice->Id;
			$invoiceline->unitValue_id = $unitValue->Id;
			$invoiceline->itemsDiscount = $discountAfterTax;
			if ($discountBeforeTax > 0){
				$discountObj = new Discount();
				$discountObj->amount = round($discountBeforeTax, 5);
				$discountObj->rate = 0;
				$discountObj->save();
				$invoiceline->discount_id = $discountObj->Id;
			}
			$invoiceline->save();

			if (is_string($line["TaxesTotal"]) && ($line["TaxID"] == "Tax14" || 
												   $line["TaxID"] == "14%" || 
												   $line["TaxID"] == "14N" || 
												   $line["TaxID"] == "14")) {
				$item1 = new TaxableItem(["taxType" => "T1", "subType" => "V009", "amount" => abs(floatval($line["TaxesTotal"]))]);
				$amount1 = ($invoiceline->salesTotal + $invoiceline->valueDifference - $discountBeforeTax);
				$amount2 = $item1->amount * 100.0 / 14.0;
				$invoiceline->valueDifference += ($amount2 - $amount1);
				$item1->rate = round($item1->amount * 100 / ($invoiceline->salesTotal + $invoiceline->valueDifference - $discountBeforeTax), 5);
				$item1->invoiceline_id = $invoiceline->Id;
				$item1->save();
				$invoiceline->total += $item1->amount;
			}
			//calculate tax reverse and update it
			/*$totalRevPercentage = 0;
			foreach($mapItem->itemTax as $reverseTax) {
				$totalRevPercentage += $reverseTax->rate;
			}
			if ($totalRevPercentage > 0)
			{
				$salesTotal = ($invoiceline->salesTotal + $invoiceline->valueDifference - $discountBeforeTax) / (1.0 + $totalRevPercentage / 100.0);
				$unitValue->amountEGP = $salesTotal / $invoiceline->quantity;
				$unitValue->save();
				$invoiceline->salesTotal = $salesTotal;
				$invoiceline->netTotal = $salesTotal;
				$invoiceline->total = $invoiceline->netTotal - $discountBeforeTax;
				foreach($mapItem->itemTax as $reverseTax) {
					$item1 = new TaxableItem(["taxType" => $reverseTax->taxType, "subType" => $reverseTax->taxSubtype, "rate" => $reverseTax->rate]);
					$item1->amount = round(($invoiceline->salesTotal + $invoiceline->valueDifference - $discountBeforeTax) * $item1->rate / 100.0, 5);
					$item1->invoiceline_id = $invoiceline->Id;
					$item1->save();
					$invoiceline->total += $item1->amount;
				}
			}*/
			//$invoiceline->valueddifference = $invoiceline->valueDifference / $invoiceline->quantity;
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
		$invoice->updateTaxTotals();
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
				 'ItemNameE' => $map['ItemNameE'],
				 'Val_Diff' => $map['Val_Diff'],
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
			->with('itemTax')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('SalesBuzz/IndexItemsMap', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->column(
                key: 'SBCode',
                label: __('SalesBuzz Code'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'ETACode',
                label: __('ETA Code'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'ItemNameA',
                label: __('Arabic Name'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'ItemNameE',
                label: __('English Name'),
                canBeHidden: true,
                hidden: false,
                sortable: true
			)->column(
                key: 'actions',
                label: __('Actions')
            )->searchInput(
                key: 'SBCode',
                label: __('SalesBuzz Code')
            )->searchInput(
                key: 'ETACode',
                label: __('ETA Code')
            )->searchInput(
                key: 'ItemNameA',
                label: __('Arabic Name')
            )->searchInput(
                key: 'ItemNameE',
                label: __('English Name')
			);
        });
	}

	public function updateItem(Request $request)
	{
		$map = $request->validate([
			'SBCode' => 'required',
			'ETACode' => 'required',
			'ItemNameA' => 'required',
			'ItemNameE' => 'required',
			'taxTypes' => 'array',
			'taxTypes.*.taxType.Code' => 'required',
			'taxTypes.*.taxSubtype.Code' => 'required',
			'taxTypes.*.percentage' => 'required',
			'Val_Diff' => 'required|numeric',
			
		]);
		SBItemMap::updateOrCreate(
			['SBCode' => $map['SBCode']],
			['SBCode' => $map['SBCode'],
			 'ETACode' => $map['ETACode'], 
			 'ItemNameA' => $map['ItemNameA'],
			 'ItemNameE' => $map['ItemNameE'],
			 'Val_Diff' => $map['Val_Diff'],
			]
		);
		SBItemTax::where('SBCode', $map['SBCode'])->delete();
		foreach($map['taxTypes'] as $taxType){
			SBItemTax::create([
				'SBCode' => $map['SBCode'],
				 'taxType' => $taxType['taxType']['Code'],
				 'taxSubtype' => $taxType['taxSubtype']['Code'],
				 'rate' => $taxType['percentage']
			]);
		}
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
		$data = DB::select("SELECT t1.Id as BID, t1.Name as BName, t2.sb_url as SBUrl, t2.sb_bu as SBBU
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
			'SBUrl' => 'required',
			'SBBU' => 'required',
		]);

		$branch_map = SBBranchMap::updateOrCreate(
			['branch_id' => $data['BID']],
			['branch_id' => $data['BID'],
			 'sb_url' => $data['SBUrl'],
			 'sb_bu' => $data['SBBU']
			]
		);

	}

	#this function is not used
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

	public function indexBranchesMap_json()
	{
		return SBBranchMap::all();
	}

	public function DownloadItemsMap()
	{
		$customers = SBItemMap::all();
		
		//render excel file now
		$reader = IOFactory::createReader('Xlsx');
		$file = $reader->load('./ExcelTemplates/ItemsMap.xlsx');
		$rowIdx = 3;
		foreach($customers as $row){
			$file->getActiveSheet()->setCellValue($this->index2(1, $rowIdx), $row->SBCode);
            $file->getActiveSheet()->setCellValue($this->index2(2, $rowIdx), $row->ItemNameA);
			$file->getActiveSheet()->setCellValue($this->index2(3, $rowIdx), $row->ItemNameE);
			$file->getActiveSheet()->setCellValue($this->index2(4, $rowIdx), $row->ETACode);
			$file->getActiveSheet()->setCellValue($this->index2(5, $rowIdx), $row->Val_Diff);
			
			$rowIdx++;
		}
		$writer = IOFactory::createWriter($file, 'Xlsx');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Map.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}


}


