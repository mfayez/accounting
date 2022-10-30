<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

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
		
		$this->AuthenticateSB($request, $data['username'], $data['password'], $data['buid']);
		if ($this->salezbuzz_cookies == ""){
			return Response::json(array(
				'code'      =>  404,
				'message'   =>  "SalesBuzz Authentication Failed"
			), 404);
		}

		$pageSize = 10;
		$skip = ($data['value'] - 1) * $pageSize;
        //get orders now
		
        $url = "https://sb.hkdist.com/salesbuzzbo/ClientBin/BI-SalesBuzz-BackOffice-Web-Services-PromotionHeaderDS.svc/json/GetAR_Order?StopLoading=false&\$skip=$skip&\$take=$pageSize&\$includeTotalCount=True";
		if ($skip == 0)
			$url = "https://sb.hkdist.com/salesbuzzbo/ClientBin/BI-SalesBuzz-BackOffice-Web-Services-PromotionHeaderDS.svc/json/GetAR_Order?StopLoading=false&\$take=$pageSize&\$includeTotalCount=True";	
		/*$timestamp = Carbon::now()
						->add(1969, 'year')
						->sub($data['period'], 'day')
						->timestamp;
		$timestamp = $timestamp * 10000000;
		*/
		
		
		$response = Http::withHeaders($this->salezbuzz_headers)
                    ->get($url);
		
		$sb_data = $response->json();
		$activity = $data['taxpayerActivityCode']['code'];
		$issuer = $data['issuer']['Id'];

		foreach($sb_data["GetAR_OrderResult"]["RootResults"] as $invoice) {
			$invoice_lines = collect($sb_data["GetAR_OrderResult"]["IncludedResults"])->where("OrderID", $invoice["OrderID"]);
			$invoice2 = Invoice::firstWhere(['internalID' => $invoice['OrderID']]);
			if (!$invoice2)
				$this->AddMissingInvoice($invoice, $invoice_lines, $issuer, $activity);
		}

		return ["totalPages" => min($data['value'] + 1, 100),
				"currentPage" => $data['value'],
				"cookie" => $this->salezbuzz_cookies
			];	
	}
	
	private function AddMissingInvoice($sb_invoice, $sb_invoice_lines, $issuer, $activity)
	{
		$invoice = new Invoice();
		$receiver = Receiver::firstWhere(['code' => $sb_invoice['CustomerNo']]);
		
		if (!$receiver) {
			$item2 = new Address();
			$item2->country = "EG";
			$item2->governate = "Cairo";
			$item2->regionCity = $sb_invoice['DeliveryAddress'];
			$item2->street = "";
			$item2->buildingNumber = "1";
			$item2->postalCode = "12345";
			$item2->save();
			$item = new Receiver();
			$item->code = $sb_invoice['CustomerNo'];
			$item->name = $sb_invoice['CustomerNameA'];
			$item->receiver_id = "0";
			$item->type = "B";
			$item2->receiver()->save($item);
			$receiver = $item;
		}
		
		$invoice->issuer_id = $issuer;
		$invoice->receiver_id = $receiver->Id;
		$invoice->statusreason = "تحميل الفاتورة من SalesBuzz";
		$invoice->documentType = "I";
		$invoice->documentTypeVersion = SETTINGS_VAL('application settings', 'invoiceVersion', '1.0');;
		$invoice->totalDiscountAmount = 0;
		$invoice->totalSalesAmount = 0;
		$invoice->netAmount = 0;
		$invoice->totalAmount = 0;
		$invoice->extraDiscountAmount = 0;
		$invoice->totalItemsDiscountAmount = 0;
		$invoice->status = "In Review";	
		$invoice->internalID = $sb_invoice['OrderID'];
		$invoice->dateTimeIssued = Carbon::createFromTimestamp(substr($sb_invoice['InvoiceDate'], 6, -7));
		$invoice->taxpayerActivityCode = $activity;
		$invoice->save();

		foreach($sb_invoice_lines as $line) {
			if (!isset($line['ItemID']))
				continue;
			$mapItem = SBItemMap::find($line['ItemID']);
			if (!$mapItem)
				continue;
			$unitValue = new Value(['currencySold' => "EGP", 
				'amountEGP' => $line['UnitPrice']
			]);
			$unitValue->save();
			$invoiceline = new InvoiceLine((array)$line);
			$invoiceline->description = $line['ItemNameA'];
			$invoiceline->itemType = "GS1";
			$invoiceline->itemCode = $mapItem->ETACode;
			$invoiceline->unitType = "EA";
			$invoiceline->quantity = $line['Qty'];
			$invoiceline->internalCode = $line['ItemID'];
			$invoiceline->salesTotal = $line['LineTotal'];
			$invoiceline->total = $line['LineTotal'];
			$invoiceline->valueDifference = 0;
			$invoiceline->totalTaxableFees = 0;
			$invoiceline->netTotal = $line['LineTotal'];
			$invoiceline->itemsDiscount = 0;
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

	public function indexMap(Request $request)
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

        return Inertia::render('SalesBuzz/Index', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'name' => __('Name'),
                'issuer_id' => __('Tax Registration ID/National ID'),
            ])->addColumns([
                'SBCode' 	=> __('SalesBuzz Code'),
                'ETACode' 	=> __('ETA Code'),
                'ItemNameA' => __('Arabic Name'),
				'ItemNameE' => __('English Name')
            ]);
        });
	}
}
