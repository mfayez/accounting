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
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\StoreCreditRequest;
use App\Http\Requests\StoreDebitRequest;

use App\Http\Traits\ETAAuthenticator;
use App\Http\Traits\ExcelWrapper;

class ETAController extends Controller
{
	use ETAAuthenticator;
	use ExcelWrapper;
	
	public function generateInvoiceNumber($invoice, $excel=false){
		if (strcmp(SETTINGS_VAL('application settings', 'automatic', '0'), '1') != 0) return;
		if ($invoice->internalID != 'automatic' && $excel == false) return;

		$values = array("YYYY", "YY", "BB", "XXXXXXX", "XXXXXX", "XXXXX", "XXXX");
		$repalcements = array("%1$04d", "%2$02d", "%3$02d", "%4$07d", "%4$06d", "%4$05d", "%4$04d");
		$template = str_replace($values, $repalcements, SETTINGS_VAL('application settings', 'invoiceTemplate', "XXXXX"));
		$branchNum = $invoice->issuer_id;
		$inv = DB::select('SELECT max(convert(internalID, unsigned integer)%100000) as LastInv FROM Invoice WHERE issuer_id = ?', [$branchNum]);
		$invNum = 1;
		if (!empty($inv))
			$invNum = 1 + $inv[0]->LastInv%100000;
		$year = intval(date("Y"));
		$year2 = $year % 100;
		$invoice->internalID = sprintf($template, $year, $year2, $branchNum, $invNum);
	}
	
	public function UploadItem(Request $request)
	{
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/codetypes/requests/codes";
		
		$temp = [];
		$extension = $request->file->extension();
		if ($extension == 'xlsx' || $extension == 'xls')
			$temp = $this->xlsxToArray($request->file, $extension);
		else if ($extension == 'csv')
			$temp = $this->csvToArray($request->file);
		else
			return json_encode(["Error" => true, "Message" => __("Unsupported File Type!")]);
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->post($url, ["items" => $temp]);
		return $response;
		
	}
	public function UploadInvoice(Request $request)
	{
		//$request->validate([
        //    'file' => 'required|file|mimes:csv',
        //]);
		$temp = [];
		$extension = $request->file->extension();
		if ($extension == 'xlsx' || $extension == 'xls')
			$temp = $this->xlsxToArray($request->file, $extension);
		else if ($extension == 'csv')
			$temp = $this->csvToArray($request->file);
		else
			return json_encode(["Error" => true, "Message" => __("Unsupported File Type!")]);
		
		$upload = new Upload();
		$upload->userId = Auth::id();
		$upload->fileName = $request->file->getClientOriginalName();
		$upload->recordsCount = count($temp);
		$upload->status = 'Uploading';
		$upload->save();
		$inserted = array(); 
		$updated = array(); 
		foreach($temp as $key=>$invoice_data)
		{
			if (isset($inserted[$invoice_data['internalID']])) {
				$temp[$key]["invoice_id"] = $inserted[$invoice_data['internalID']];
				continue;
			}
			$invoice = new Invoice($invoice_data);
			$invoice->documentType = "I";
			$invoice->documentTypeVersion = SETTINGS_VAL('application settings', 'invoiceVersion', '1.0');;
			$invoice->totalDiscountAmount = 0;
			$invoice->totalSalesAmount = 0;
			$invoice->netAmount = 0;
			$invoice->totalAmount = 0;
			$invoice->extraDiscountAmount = 0;
			$invoice->totalItemsDiscountAmount = 0;
			$invoice->status = "In Review";	
			$invoice->statusreason = "Excel Upload";
			$invoice->upload_id = $upload->Id;
			$this->generateInvoiceNumber($invoice, true);
			$invoice->save();
			$temp[$key]["invoice_id"] = $invoice->Id;
			$inserted[$invoice_data['internalID']] = $invoice->Id;
		}
		foreach($temp as $key=>$invoice_data)
		{
			$item = ETAItem::where("itemCode", "=", $invoice_data["itemCode"])->first();
			if (!$item){
				$temp[$key]["hasError"] = true;
				$temp[$key]["error"] = __("Item") ." ". $invoice_data["itemCode"]. " ". __("not found!");
				continue;
			}
			$temp[$key]["hasError"] = false;
			
			$unitValue = new Value($invoice_data);
			$unitValue->save();
			$invoiceline = new InvoiceLine($invoice_data);
			$invoiceline->unitValue_id = $unitValue->Id;
			$invoiceline->itemType = $item->codeTypeName;//"EGS"
			$invoiceline->description = $item->codeNamePrimaryLang;
			$invoiceline->internalCode = $item->Id;
			$invoiceline->itemsDiscount = $invoice_data["itemsDiscount"];
			$invoiceline->netTotal = $invoice_data["salesTotal"];// - $invoiceline->itemsDiscount;
			$invoiceline->valueDifference = 0;
			$invoiceline->totalTaxableFees = 0;
			
			$invoiceline->save();
			/*
			foreach($invoice_data as $key=>$taxVal)
			{
				$dicTax = Str::of($key)->split('/[()]+/');
				if ($dicTax[0][0] == 'T'){
					if (intval(substr($dicTax[0], 1)) < 13 && intval(substr($dicTax[0], 1)) > 0){
						$item1 = new TaxableItem(["taxType" => $dicTax[0], "subType" => $dicTax[1], "amount" => floatval($taxVal)]);
						if ($invoiceline->salesTotal > 0)
							$item1->rate = $item1->amount * 100 / $invoiceline->salesTotal;
						else
							$item1->rate = 0;
		     			$item1->invoiceline_id = $invoiceline->Id;
		                $item1->save();
					}
				}
			}*/
			if ($invoice_data["T1(V009)"] > 0) {
				$item1 = new TaxableItem(["taxType" => "T1", "subType" => "V009", "amount" => floatval($invoice_data["T1(V009)"])]);
				$item1->rate = round($item1->amount * 100 / ($invoiceline->salesTotal - $invoiceline->itemsDiscount), 2);
				$item1->invoiceline_id = $invoiceline->Id;
				$item1->save();
			}
			if ($invoice_data["T2(Tbl01)"] > 0) {
				$item1 = new TaxableItem(["taxType" => "T2", "subType" => "Tbl01", "amount" => floatval($invoice_data["T2(Tbl01)"])]);
				$item1->rate = round($item1->amount * 100 / ($invoiceline->salesTotal - $invoiceline->itemsDiscount), 2);
				$item1->invoiceline_id = $invoiceline->Id;
				$item1->save();
			}
			$invoiceline->invoice->normalize();
			$invoiceline->invoice->save();
			
		}
		foreach($temp as $key=>$invoice_data)
		{
			if (!isset($updated[$invoice_data['internalID']])) {
				$invoice = Invoice::find($invoice_data['invoice_id']);
				$invoice->updateTaxTotals();
				$updated[$invoice_data['internalID']] = $invoice->Id;
			}
		}
		
		$upload->status = 'Review';
		$upload->save();
		return $temp;
        //$fileName = time().'.'.$request->file->extension();  
     
        //$request->file->move(public_path('file'), $fileName);
  
        /* Store $fileName name in DATABASE from HERE */
        //File::create(['name' => $fileName])
    
	}

	public function AddInvoice(StoreInvoiceRequest $request)
	{
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documentsubmissions";
		$data = $request->validated();
		//remove extra attributes, no need but you can get them from git history
		$data['dateTimeIssued'] = $data['dateTimeIssued'] . ":00Z";
		$data['taxpayerActivityCode'] = $data['taxpayerActivityCode']['code'];
		$data['totalSalesAmount'] = floatval($data['totalSalesAmount']);
		$data['totalDiscountAmount'] = floatval($data['totalDiscountAmount']);
		$data['netAmount'] = floatval($data['netAmount']);
		$data['totalAmount'] = floatval($data['totalAmount']);
		$data['totalItemsDiscountAmount'] = floatval($data['totalItemsDiscountAmount']);
		$data['extraDiscountAmount'] = floatval($data['extraDiscountAmount']);
		foreach($data['invoiceLines'] as $key=>$line){
			$data['invoiceLines'][$key]['salesTotal'] = floatval($line['salesTotal']);
			$data['invoiceLines'][$key]['total'] = floatval($line['total']);
			$data['invoiceLines'][$key]['valueDifference'] = floatval($line['valueDifference']);
			$data['invoiceLines'][$key]['totalTaxableFees'] = floatval($line['totalTaxableFees']);
			$data['invoiceLines'][$key]['netTotal'] = floatval($line['netTotal']);
			$data['invoiceLines'][$key]['itemsDiscount'] = floatval($line['itemsDiscount']);
			$data['invoiceLines'][$key]['unitValue']['amountEGP'] = floatval($line['unitValue']['amountEGP']);
		}
		//return ["documents" => array($data)];
		$data['status'] = "In Review";
		$data['statusReason'] = "Manual Entry";
		$data['issuer_id'] = $data['issuer']['Id'];
		$data['receiver_id'] = $data['receiver']['Id'];
		$invoice = Invoice::updateOrCreate(['Id' => $request->input('Id', -1)], $data);
		if ($request->isMethod('post') || $invoice->internalID == 'automatic') {
			$this->generateInvoiceNumber($invoice);
			$invoice->save();
		}
		foreach($invoice->invoiceLines as $line)
		{
			//if($line->discount)
			$line->discount()->delete();
			$delme = $line->unitValue;
			//if($line->taxableItems)
			$line->taxableItems()->delete();
			$line->delete();
			if($delme) $delme->delete();
		}
		//if($invoice->taxTotals)
		$invoice->taxTotals()->delete();

		//$invoice->issuer_id = $data['issuer']['Id'];
		//$invoice->receiver_id = $data['receiver']['Id'];
		//$invoice->status = "In Review";
		//$invoice->statusreason = "Manual Entry";
		//$invoice->save();	
		foreach($data['invoiceLines'] as $line) {
			$unitValue = new Value($line['unitValue']);
			if (!isset($line['amountSold']))
				$unitValue->amountSold = null;
			if (!isset($line['currencyExchangeRate']))
				$unitValue->currencyExchangeRate = null;
			$unitValue->save();
			$invoiceline = new InvoiceLine($line);
			$invoiceline->invoice_id = $invoice->Id;
			$invoiceline->unitValue_id = $unitValue->Id;
			$invoiceline->save();
			foreach($line['taxableItems'] as $taxitem) {
				$item = new TaxableItem($taxitem);
				$item->invoiceline_id = $invoiceline->Id;
				$item->save();
			}
		}
		foreach($data["taxTotals"] as $totalTax) {
			$taxTotal = new TaxTotal($totalTax);
			$taxTotal->invoice_id = $invoice->Id;
			$taxTotal->save();
		}
		return $invoice;
		//$this->AuthenticateETA($request);
		//$response = Http::withToken($this->token)->post($url, ["documents" => array($data)]);
		//return $response;
	}
	
	public function AddCredit(StoreCreditRequest $request)
	{
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documentsubmissions";
		$data = $request->validated();
		//remove extra attributes, no need but you can get them from git history
		$data['taxpayerActivityCode'] = $data['taxpayerActivityCode'];
		$data['totalSalesAmount'] = floatval($data['totalSalesAmount']);
		$data['totalDiscountAmount'] = floatval($data['totalDiscountAmount']);
		$data['netAmount'] = floatval($data['netAmount']);
		$data['totalAmount'] = floatval($data['totalAmount']);
		$data['totalItemsDiscountAmount'] = floatval($data['totalItemsDiscountAmount']);
		$data['extraDiscountAmount'] = floatval($data['extraDiscountAmount']);
		foreach($data['invoiceLines'] as $key=>$line){
			$data['invoiceLines'][$key]['salesTotal'] = floatval($line['salesTotal']);
			$data['invoiceLines'][$key]['total'] = floatval($line['total']);
			$data['invoiceLines'][$key]['valueDifference'] = floatval($line['valueDifference']);
			$data['invoiceLines'][$key]['totalTaxableFees'] = floatval($line['totalTaxableFees']);
			$data['invoiceLines'][$key]['netTotal'] = floatval($line['netTotal']);
			$data['invoiceLines'][$key]['itemsDiscount'] = floatval($line['itemsDiscount']);
			$data['invoiceLines'][$key]['unitValue']['amountEGP'] = floatval($line['unitValue']['amountEGP']);
		}
		//return ["documents" => array($data)];
		$data['status'] = "In Review";
		$data['statusReason'] = "Manual Entry";
		$data['issuer_id'] = $data['issuer']['Id'];
		$data['receiver_id'] = $data['receiver']['Id'];
		$invoice = new Invoice($data);
		$invoice->save();
		foreach($data['invoiceLines'] as $line) {
			$unitValue = new Value($line['unitValue']);
			if (!isset($line['amountSold']))
				$unitValue->amountSold = null;
			if (!isset($line['currencyExchangeRate']))
				$unitValue->currencyExchangeRate = null;
			$unitValue->save();
			$invoiceline = new InvoiceLine($line);
			$invoiceline->invoice_id = $invoice->Id;
			$invoiceline->unitValue_id = $unitValue->Id;
			$invoiceline->save();
			foreach($line['taxableItems'] as $taxitem) {
				$item = new TaxableItem($taxitem);
				$item->invoiceline_id = $invoiceline->Id;
				$item->save();
			}
		}
		foreach($data["taxTotals"] as $totalTax) {
			$taxTotal = new TaxTotal($totalTax);
			$taxTotal->invoice_id = $invoice->Id;
			$taxTotal->save();
		}
		return $invoice;
		//$this->AuthenticateETA($request);
		//$response = Http::withToken($this->token)->post($url, ["documents" => array($data)]);
		//return $response;
	}

	public function AddDebit(StoreDebitRequest $request)
	{
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documentsubmissions";
		$data = $request->validated();
		//remove extra attributes, no need but you can get them from git history
		$data['taxpayerActivityCode'] = $data['taxpayerActivityCode'];
		$data['totalSalesAmount'] = floatval($data['totalSalesAmount']);
		$data['totalDiscountAmount'] = floatval($data['totalDiscountAmount']);
		$data['netAmount'] = floatval($data['netAmount']);
		$data['totalAmount'] = floatval($data['totalAmount']);
		$data['totalItemsDiscountAmount'] = floatval($data['totalItemsDiscountAmount']);
		$data['extraDiscountAmount'] = floatval($data['extraDiscountAmount']);
		foreach($data['invoiceLines'] as $key=>$line){
			$data['invoiceLines'][$key]['salesTotal'] = floatval($line['salesTotal']);
			$data['invoiceLines'][$key]['total'] = floatval($line['total']);
			$data['invoiceLines'][$key]['valueDifference'] = floatval($line['valueDifference']);
			$data['invoiceLines'][$key]['totalTaxableFees'] = floatval($line['totalTaxableFees']);
			$data['invoiceLines'][$key]['netTotal'] = floatval($line['netTotal']);
			$data['invoiceLines'][$key]['itemsDiscount'] = floatval($line['itemsDiscount']);
			$data['invoiceLines'][$key]['unitValue']['amountEGP'] = floatval($line['unitValue']['amountEGP']);
		}
		//return ["documents" => array($data)];
		$data['status'] = "In Review";
		$data['statusReason'] = "Manual Entry";
		$data['issuer_id'] = $data['issuer']['Id'];
		$data['receiver_id'] = $data['receiver']['Id'];
		$invoice = new Invoice($data);
		$invoice->save();
		foreach($data['invoiceLines'] as $line) {
			$unitValue = new Value($line['unitValue']);
			if (!isset($line['amountSold']))
				$unitValue->amountSold = null;
			if (!isset($line['currencyExchangeRate']))
				$unitValue->currencyExchangeRate = null;
			$unitValue->save();
			$invoiceline = new InvoiceLine($line);
			$invoiceline->invoice_id = $invoice->Id;
			$invoiceline->unitValue_id = $unitValue->Id;
			$invoiceline->save();
			foreach($line['taxableItems'] as $taxitem) {
				$item = new TaxableItem($taxitem);
				$item->invoiceline_id = $invoiceline->Id;
				$item->save();
			}
		}
		foreach($data["taxTotals"] as $totalTax) {
			$taxTotal = new TaxTotal($totalTax);
			$taxTotal->invoice_id = $invoice->Id;
			$taxTotal->save();
		}
		return $invoice;
		//$this->AuthenticateETA($request);
		//$response = Http::withToken($this->token)->post($url, ["documents" => array($data)]);
		//return $response;
	}

	public function CancelInvoice(Request $request)
	{
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/state/%s/state";
		$url = sprintf($url, $request->input("uuid"));
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->put($url, [
			"status" => $request->input("status"),
			"reason" => $request->input("reason")
		]);
		if ($response->successful()) {
			$inv1 = Invoice::where("uuid", "=", $request->input("uuid"))->first();
			$inv2 = ETAInvoice::where("uuid", "=", $request->input("uuid"))->first();
			if($inv1){
				$inv1->status = "processing";
				$inv1->statusreason = $request->input("reason");
				$inv1->save();
			}
			if($inv2){
				$inv2->status = $request->input("status");
				$inv2->save();
			}
			return "تم قبول الطلب من قبل المصلحة";
		}
		
		return "تم رفض الطلب من قبل المصلحة";
	}

	public function SyncInvoices(Request $request)
	{
		//TODO check if there are no branches
		$myid = Issuer::first()->issuer_id;
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/recent";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"PageSize" => "10",
			"PageNo" => $request->input("value")
		]);
		$collection = $response['result'];
		foreach($collection as $item) {
			if ((isset($item['issuer_id']) && $item['issuer_id'] == $myid) ||
				(isset($item['issuerId']) && $item['issuerId'] == $myid))
			{
				try{
					$invoice2 = Invoice::firstWhere(['uuid' => $item['uuid']]);
					if ($invoice2)
					{
						$invoice2->status = $item['status'];
						$invoice2->statusreason = $item['documentStatusReason'];
						$invoice2->save();
					} else {
						$this->AddMissingInvoice($request, $item['uuid']);
					}
				} catch (Exception $e) {}
			} else {
				try{
					$invoice2 = ETAInvoice::firstWhere(['uuid' => $item['uuid']]);
					if ($invoice2)
					{
						$invoice2->status = $item['status'];
						//$invoice2->statusreason = $item['documentStatusReason'];
						$invoice2->save();
					} else {
						//recover missing item
						$this->AddMissingETAInvoice($request, $item['uuid']);
					}
				} catch (Exception $e) {}
			}
		};
		return $response['metadata'];
	}
	public function SyncReceivedInvoices(Request $request)
	{
		return;
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/recent";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"PageSize" => "10",
			"PageNo" => "1",
			"InvoiceDirection" => "received"	
		]);
		$collection = $response['result'];
		foreach($collection as $item) {
			try{
				$invoice2 = ETAInvoice::firstWhere(['uuid' => $item['uuid']]);
				if ($invoice2)
				{
					$invoice2->status = $item['status'];
					//$invoice2->statusreason = $item['documentStatusReason'];
					$invoice2->save();
				} else {
					//recover missing item
					$this->AddMissingETAInvoice($request, $item['uuid']);
				}
			} catch (Exception $e) {}
		};
		return $response['metadata'];
	}

	public function SyncIssuedInvoices(Request $request)
	{
		return $this->SyncInvoices($request);
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/recent";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"PageSize" => "10",
			"PageNo" => $request->input("value")
			,"InvoiceDirection" => "sent"	
		]);
		$collection = $response['result'];
		foreach($collection as $item) {
			try{
				$invoice2 = Invoice::firstWhere(['uuid' => $item['uuid']]);
				if ($invoice2)
				{
					$invoice2->status = $item['status'];
					$invoice2->statusreason = $item['documentStatusReason'];
					$invoice2->save();
				} else {
					$this->AddMissingInvoice($request, $item['uuid']);
				}
			} catch (Exception $e) {}

		};
		return $response['metadata'];
	}

	public function SyncItems(Request $request)
	{
		$myid = Issuer::first()->issuer_id;
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/codetypes/".$request->input("type")."/codes";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"Ps" => "100",
			"Pn" => $request->input("value"),
			"TaxpayerRIN" => $myid
		]);
		$collection = $response['result'];
		foreach($collection as $item) {
		    //if ($item["codeTypeNamePrimaryLang"] == "GS1")
			$item2 = ETAItem::updateOrCreate(['itemCode' => $item['codeLookupValue']], $item);
		    //else
			//$item2 = ETAItem::updateOrCreate(['itemCode' => $item['itemCode']], $item);

			if ($item['ownerTaxpayer']){
		    	$item2->ownerTaxpayerrin = $item['ownerTaxpayer']['rin'];
	            $item2->ownerTaxpayername = $item['ownerTaxpayer']['name'];
        	    $item2->ownerTaxpayernameAr = $item['ownerTaxpayer']['nameAr'];
			}
			if ($item['requesterTaxpayer']){
	            $item2->requesterTaxpayerrin = $item['requesterTaxpayer']['rin'];
        	    $item2->requesterTaxpayername = $item['requesterTaxpayer']['name'];
	            $item2->requesterTaxpayernameAr = $item['requesterTaxpayer']['nameAr'];
			}
			if ($item['codeCategorization']){
        	    $item2->codeCategorizationlevel1id = $item['codeCategorization']['level1']['id'];
	            $item2->codeCategorizationlevel1name = $item['codeCategorization']['level1']['name'];
        	    $item2->codeCategorizationlevel1nameAr = $item['codeCategorization']['level1']['nameAr'];
	            $item2->codeCategorizationlevel2id = $item['codeCategorization']['level2']['id'];
        	    $item2->codeCategorizationlevel2name = $item['codeCategorization']['level2']['name'];
	            $item2->codeCategorizationlevel2nameAr = $item['codeCategorization']['level2']['nameAr'];
        	    $item2->codeCategorizationlevel3id = $item['codeCategorization']['level3']['id'];
	            $item2->codeCategorizationlevel3name = $item['codeCategorization']['level3']['name'];
        	    $item2->codeCategorizationlevel3nameAr = $item['codeCategorization']['level3']['nameAr'];
	            $item2->codeCategorizationlevel4id = $item['codeCategorization']['level4']['id'];
        	    $item2->codeCategorizationlevel4name = $item['codeCategorization']['level4']['name'];
	            $item2->codeCategorizationlevel4nameAr = $item['codeCategorization']['level4']['nameAr'];
			}
		    if ($item2->codeTypeName == null)
			    $item2->codeTypeName = $item["codeTypeNamePrimaryLang"];
		    if ($item2->descriptionPrimaryLang == null)
			    $item2->descriptionPrimaryLang = $item2->codeNamePrimaryLang;
		    if ($item2->descriptionSecondaryLang == null)
			    $item2->descriptionSecondaryLang = $item2->codeNameSecondaryLang;
		    $item2->save();
		};
		return $response['metadata'];
	}

	public function SyncItemsRequests(Request $request)
	{
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/codetypes/requests/my";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"Ps" => "100",
			"Pn" => $request->input("value")
		]);
		if (!isset($response['result']))
			return json_encode(["totalPages" => 0, "totalCount" => 0]);
		$collection = $response['result'];
		foreach($collection as $item) {
			$item2 = ETAItem::updateOrCreate(['itemCode' => $item['itemCode']], $item);
			$item2->ownerTaxpayerrin = $item['ownerTaxpayer']['rin'];
            $item2->ownerTaxpayername = $item['ownerTaxpayer']['name'];
            $item2->ownerTaxpayernameAr = $item['ownerTaxpayer']['nameAr'];
            $item2->requesterTaxpayerrin = $item['requesterTaxpayer']['rin'];
            $item2->requesterTaxpayername = $item['requesterTaxpayer']['name'];
            $item2->requesterTaxpayernameAr = $item['requesterTaxpayer']['nameAr'];
            $item2->codeCategorizationlevel1id = $item['codeCategorization']['level1']['id'];
            $item2->codeCategorizationlevel1name = $item['codeCategorization']['level1']['name'];
            $item2->codeCategorizationlevel1nameAr = $item['codeCategorization']['level1']['nameAr'];
            $item2->codeCategorizationlevel2id = $item['codeCategorization']['level2']['id'];
            $item2->codeCategorizationlevel2name = $item['codeCategorization']['level2']['name'];
            $item2->codeCategorizationlevel2nameAr = $item['codeCategorization']['level2']['nameAr'];
            $item2->codeCategorizationlevel3id = $item['codeCategorization']['level3']['id'];
            $item2->codeCategorizationlevel3name = $item['codeCategorization']['level3']['name'];
            $item2->codeCategorizationlevel3nameAr = $item['codeCategorization']['level3']['nameAr'];
            $item2->codeCategorizationlevel4id = $item['codeCategorization']['level4']['id'];
            $item2->codeCategorizationlevel4name = $item['codeCategorization']['level4']['name'];
            $item2->codeCategorizationlevel4nameAr = $item['codeCategorization']['level4']['nameAr'];
			$item2->save();
		};
		return $response['metadata'];
	}

	public function AddItem(Request $request)
	{
		$url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/codetypes/requests/codes";
		$data = $request->validate([
			'codeType'		=> ['required', 'string', Rule::in(['EGS', 'GS1'])],
			'parentCode'	=> ['required', 'integer'],
			'itemCode'		=> ['required', 'regex:/EG-[0-9]+-[A-Za-z0-9_]+/'],
			'codeName'		=> ['required', 'string', 'max:255'],
			'codeNameAr'	=> ['required', 'string', 'max:255'],
			'activeFrom'	=> ['required', 'date'],
			'activeTo'		=> ['required', 'date'],
			'description'	=> ['required', 'string', 'max:255'],
			'descriptionAr'	=> ['required', 'string', 'max:255'],
			'requestReason'	=> ['required', 'string', 'max:255']
        ]);
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->post($url, ["items" => array($data)]);
		return $response;
	}

	public function indexIssued(Request $request, $upload_id = null)
	{
		$columns = $request->query("columns", []);
		$remember = $request->query("remember", "yes");
		if (count($columns) == 0 && $remember == 'yes')
		{
			$columns_str = SETTINGS_VAL(Auth::user()->name, "index.issued.columns", "[]");
			$columns = json_decode($columns_str);
			if (count($columns) > 0)
				return redirect()->route('eta.invoices.sent.index', ["columns" => $columns]);
		}
		SETTINGS_SET_VAL(Auth::user()->name, "index.issued.columns", json_encode($columns));

		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('totalDiscountAmount', '=', "{$value}")
                    ->orWhere('netAmount', '=', "{$value}")
                    ->orWhere('internalID', '=', "{$value}");
            });
        });

		$items = QueryBuilder::for(Invoice::class)
			->with("receiver")
			->with("issuer")
			->with("invoiceLines")
			->with("invoiceLines.taxableItems")
			->with('invoiceLines.unitValue')
			->whereNotNull('issuer_id')
			->where(function ($query) use ($upload_id){
				if ($upload_id)
            		$query->where('upload_id', '=', $upload_id);
           	})
			//->join("Receiver", "Invoice.receiver_id", "Receiver.Id")
			//->join("Issuer", "Invoice.issuer_id", "Issuer.Id")
            ->defaultSort('-Invoice.Id')
            ->allowedSorts(['status' , 'internalID' , 'totalAmount' , 'netAmount', 'dateTimeIssued'])
            ->allowedFilters(['status', 'internalID', $globalSearch])
            ->paginate(20)
            ->withQueryString();
        return Inertia::render('Invoices/Index', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
				'internalID'	=>	__('Internal ID'),
				'status'	=>	__('Status')
			])->addColumns([
                'internalID'	=> __('Internal ID'),
				'receiver.name' => __('Receiver'),
				'receiver.receiver_id'	=> __('Customer Registration Number'),
				'totalAmount' => __('Total Amount'),
				'dateTimeIssued' => __('Issued At'),
				'netAmount' => __('Net Amount'),
				'status'		=> __('Status'),
				'statusReason'		=> __('ETA Comments'),
            ]);
        });
    }
    public function indexItems()
    {
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('itemCode', 'LIKE', "%{$value}%")
                    ->orWhere('codeNamePrimaryLang', 'LIKE', "%{$value}%")
                    ->orWhere('codeNameSecondaryLang', 'LIKE', "%{$value}%")
                    ->orWhere('descriptionPrimaryLang', 'LIKE', "%{$value}%")
					->orWhere('descriptionSecondaryLang', 'LIKE', "%{$value}%")
					->orWhere('levelName', 'LIKE', "%{$value}%");
            });
        });

		$items = QueryBuilder::for(ETAItem::class)
            ->defaultSort('id')
            ->allowedSorts(['codeTypeName', 'codeID', 'itemCode', 'codeNamePrimaryLang', 'codeNameSecondaryLang', 'descriptionPrimaryLang',     
							  'descriptionSecondaryLang', 'parentCodeID', 'parentItemCode', 'parentCodeNamePrimaryLang', 'parentCodeNameSecondaryLang',
							  'parentLevelName', 'levelName', 'requestCreationDateTimeUtc', 'codeCreationDateTimeUtc', 'activeFrom',
							  'activeTo', 'active', 'status', 'statusReason'])
            ->allowedFilters(['codeTypeName', 'codeID', 'itemCode', 'codeNamePrimaryLang', 'codeNameSecondaryLang', 'descriptionPrimaryLang',     
							  'descriptionSecondaryLang', 'parentCodeID', 'parentItemCode', 'parentCodeNamePrimaryLang', 'parentCodeNameSecondaryLang',
							  'parentLevelName', 'levelName', 'requestCreationDateTimeUtc', 'codeCreationDateTimeUtc', 'activeFrom',
							  'activeTo', 'active', 'status', 'statusReason', $globalSearch])
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('ETA/Items/Index', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
				'codeID'	=>	'Code ID'
			])->addColumns([
                'codeTypeName'	=>	__('Type')	,
				'codeID'	=>	__('ETA Code')	,
				'itemCode'	=>	__('Standard Code')	,
				'codeNamePrimaryLang'	=>	__('Name 1'),
				'codeNameSecondaryLang'	=>	__('Name 2')	,
				'descriptionPrimaryLang'	=>	__('Description')	,
				'descriptionSecondaryLang'	=>	__('Description')	,
				'parentCodeID'	=>	__('ETA Parent Code')	,
				'parentItemCode'	=>	__('Standard Parent Code')	,
				'parentCodeNamePrimaryLang'	=>	__('Parent Name 1')	,
				'parentCodeNameSecondaryLang'	=>	__('Parent Name 2')	,
				'parentLevelName'	=>	__('Parent Level Name')	,
				'levelName'	=>	__('Level Name')	,
				'requestCreationDateTimeUtc'	=>	__('Request Time')	,
				'codeCreationDateTimeUtc'	=>	__('Creation Time')	,
				'activeFrom'	=>	__('Active Date')	,
				'activeTo'	=>	__('Expire Date')	,
				'active'	=>	__('Active Status')	,
				'status'	=>	__('Status')	,
				'statusReason'	=>	__('ETA Comments')	,
            ]);
        });
    }

    public function indexItems_json()
    {
		return ETAItem::all()->toArray();
	}
	
	public function UpdateInvoices(){
		$urlbase = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/%s/raw";
		$invoices = Invoice::where('status', '=', 'Invalid')
					->where('statusReason', '=', '')
					->get();
		
		$this->AuthenticateETA2();
		foreach($invoices as $invoice) {
			$url = sprintf($urlbase, $invoice->uuid);
			$response = Http::withToken($this->token)->get($url);
			$errors = "";
			$steps = $response['validationResults']['validationSteps'];
			foreach($steps as $step) {
				if ($step['status'] != "Invalid")
					continue;
				//if(!$step['error']) continue;
				$errors = $errors . "," .  $step['error']['error'];
				if ($step['error']['innerError'])
					foreach($step['error']['innerError'] as $inner)
						$errors = $errors . "," .  $inner['error'];
			}
			$invoice->statusreason = $errors;
			$invoice->save();	
		}
		
		//error_log(json_encode($invoices));
	}

	public function AddMissingETAInvoice($request, $uuid) {
		$urlbase = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/%s/raw";
		$this->AuthenticateETA($request);
		$url = sprintf($urlbase, $uuid);
		$response = Http::withToken($this->token)->get($url);
		//$document = json_decode($response['document']);
		//error_log($response);
		$invoice = new ETAInvoice();
		//if ($response['status'] !== 'Valid') return;
		foreach($invoice->getFillable() as $field)
			$invoice[$field] = null;
		$invoice->createdByUserId = 'N/A';

		foreach($invoice->getFillable() as $field)
			if(isset($response[$field]))
				$invoice[$field] = $response[$field];
		
		$errors = "";
		$steps = $response['validationResults']['validationSteps'];
		foreach($steps as $step) {
			if ($step['status'] != "Invalid")
				continue;
			//if(!$step['error']) continue;
			$errors = $errors . "," .  $step['error']['error'];
			if ($step['error']['innerError'])
				foreach($step['error']['innerError'] as $inner)
					$errors = $errors . "," .  $inner['error'];
		}
		$invoice->reviewer = "";
		$invoice->comment = "";
		//$invoice->statusreason = $errors;
		$invoice->save();
		/*
		foreach($document->invoiceLines as $line) {
			$unitValue = new Value((array)$line->unitValue);
			$unitValue->save();
			$invoiceline = new InvoiceLine((array)$line);
			$invoiceline->invoice_id = $invoice->Id;
			$invoiceline->unitValue_id = $unitValue->Id;
			$invoiceline->save();
			foreach($line->taxableItems as $taxitem) {
				$item = new TaxableItem((array)$taxitem);
				$item->invoiceline_id = $invoiceline->Id;
				$item->save();
			}
		}
		foreach($document->taxTotals as $totalTax) {
			$taxTotal = new TaxTotal((array)$totalTax);
			$taxTotal->invoice_id = $invoice->Id;
			$taxTotal->save();
		}*/
	}


	public function AddMissingInvoice($request, $uuid) {
		$urlbase = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/%s/raw";
		$this->AuthenticateETA($request);
		$url = sprintf($urlbase, $uuid);
		$response = Http::withToken($this->token)->get($url);
		$document = json_decode($response['document']);
		//error_log($response['uuid']);
		$invoice = new Invoice((array)$document);
		if (strcasecmp($response['status'], 'Valid') != 0 &&
			strcasecmp($response['status'], 'Rejected') != 0 &&
			strcasecmp($response['status'], 'Cancelled') != 0 
		) return;

		$issuer = Issuer::where('name', '=', $document->issuer->name)->first();
		$receiver = Receiver::where('name', '=', $document->receiver->name)->first();
		//$receiver = Receiver::where('receiver_id', '=', $document->receiver->id)->first();
		$invoice->status = $response['status'];
		$invoice->uuid = $response["uuid"];
		$invoice->submissionUUID = $response["submissionUUID"];
		$invoice->longId = $response["longId"];
		//dd($invoice);
		if (!$issuer)
		{
			$item2 = new Address((array)$document->issuer->address);
        	$item2->save();
	        $item = new Issuer((array)$document->issuer);
			$item->issuer_id = $document->issuer->id;
			$item->id = null;
        	$item2->issuer()->save($item);
			$issuer = $item;
		}
		$invoice->issuer_id = $issuer->Id;

		if (!$receiver)
		{
			$item2 = new Address((array)$document->receiver->address);
        	$item2->save();
	        $item = new Receiver((array)$document->receiver);
			$item->receiver_id = isset($document->receiver->id) ? $document->receiver->id : 0;
			$item->id = null;
			$item->code = 0;
        	$item2->receiver()->save($item);
			$receiver = $item;
		}
		$invoice->receiver_id = $receiver->Id;
		
		$errors = "";
		$steps = $response['validationResults']['validationSteps'];
		foreach($steps as $step) {
			if ($step['status'] != "Invalid")
				continue;
			//if(!$step['error']) continue;
			$errors = $errors . "," .  $step['error']['error'];
			if ($step['error']['innerError'])
				foreach($step['error']['innerError'] as $inner)
					$errors = $errors . "," .  $inner['error'];
		}
		$invoice->statusreason = $errors;
		$invoice->save();

		foreach($document->invoiceLines as $line) {
			$unitValue = new Value((array)$line->unitValue);
			$unitValue->save();
			$invoiceline = new InvoiceLine((array)$line);
			$invoiceline->invoice_id = $invoice->Id;
			$invoiceline->unitValue_id = $unitValue->Id;
			$invoiceline->save();
			foreach($line->taxableItems as $taxitem) {
				$item = new TaxableItem((array)$taxitem);
				$item->invoiceline_id = $invoiceline->Id;
				$item->save();
			}
		}
		foreach($document->taxTotals as $totalTax) {
			$taxTotal = new TaxTotal((array)$totalTax);
			$taxTotal->invoice_id = $invoice->Id;
			$taxTotal->save();
		}
	}

	public function LoadMissingInvoices() {	
		$urlbase = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/%s/raw";
		$oldinv = Invoice::whereNotNull('uuid')->pluck('uuid');
		$missing = ETAInvoice::whereNotIn('uuid', $oldinv)->pluck('uuid');
		
		$this->AuthenticateETA2();
		foreach($missing as $inv) {
			$url = sprintf($urlbase, $inv);
			$response = Http::withToken($this->token)->get($url);
			$document = json_decode($response['document']);
			error_log($response['uuid']);
			$invoice = new Invoice((array)$document);

			$issuer = Issuer::where('issuer_id', '=', $document->issuer->id)->first();
			$receiver = Receiver::where('receiver_id', '=', $document->receiver->id)->first();
			$invoice->status = $response['status'];
			$invoice->uuid = $response["uuid"];
			$invoice->submissionUUID = $response["submissionUUID"];
			$invoice->longId = $response["longId"];
			if ($issuer)
				$invoice->issuer_id = $issuer->Id;
			//else
			//	$invoice->issuer_id = AddIssuer($document->issuer);
			if ($receiver)
				$invoice->receiver_id = $receiver->Id;
			//else
			//	$invoice->receiver_id = AddReceiver($document->receiver);
			$errors = "";
			$steps = $response['validationResults']['validationSteps'];
			foreach($steps as $step) {
				if ($step['status'] != "Invalid")
					continue;
				//if(!$step['error']) continue;
				$errors = $errors . "," .  $step['error']['error'];
				if ($step['error']['innerError'])
					foreach($step['error']['innerError'] as $inner)
						$errors = $errors . "," .  $inner['error'];
			}
			$invoice->statusreason = $errors;
			$invoice->save();
		}
	}

    public function indexExcel()
    {
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('fileName', 'LIKE', "%{$value}%")->orWhere('status', 'LIKE', "%{$value}%");
            });
        });

        $items = QueryBuilder::for(Upload::class)
			->with('user')
			->whereNotIn('status', ['canceled'])
        	->defaultSort('-created_at')
            ->allowedSorts(['Id', 'fileName', 'status', 'recordsCount'])
            ->allowedFilters(['fileName', 'user.name', $globalSearch])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Invoices/UploadIndex', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'fileName' => 'Name',
                'user.name' => 'Uploader',
            ])->addColumns([
                'fileName' => 'File Name',
                'created_at' => 'Upload Time',
				'recordsCount' => 'Number of Items',
				'user.name' => 'Uploader',
				'status' => 'Status'
            ]);
        });
    }

	function CancelUpload(Request $request){
		$upload = Upload::findOrFail($request->input('id'));
		$upload->status = "Canceled";
		$upload->save();
		/*foreach($upload->invoices as $inv){
			foreach($inv->invoiceLines as $line)
			{
				$line->discount->delete();
				$line->unitValue->delete();
				$line->taxableItems->delete();
				$line->delete();
			}
			$inv->taxTotals->delete();
			$inv->delete();
		}
		$upload->delete();*/
	}

	public function ApproveInvoice(Request $request)
	{
		$inv = Invoice::findOrFail($request->input('Id'));
		$inv->status = 'approved';
		$inv->statusreason = 'Approved by ' . Auth::user()->name;
		$inv->save();
		return "Invoice approved";
	}
	
	public function DeleteInvoice(Request $request)
	{
		$inv = Invoice::findOrFail($request->input('Id'));
		foreach($inv->invoiceLines as $line) {
			//if ($line->discount) $line->discount->delete();
			//if ($line->taxableItems) $line->taxableItems->delete();
			$line->discount()->delete();
			$line->taxableItems()->delete();
			$line->delete();
			$line->unitValue()->delete();
		}
		//if ($inv->taxTotals) 
		$inv->taxTotals()->delete();
		$inv->delete();
		return "Invoice Deleted";
	}

	public function saveCopy(Request $request) {

		$request->validate([
			'Id' => 'required|integer'
		]);

		$invoice = Invoice::find($request->Id);

		$invoiceClone = $invoice->replicate();

		$invoiceClone->created_at = now();

		$invoiceClone->updated_at = now();

		$invoiceClone->save();

		foreach($invoice->invoiceLines as $line) {
			
			$unitValue = Value::find($line->unitValue_id);
			
			$unitValueClone = $unitValue->replicate();

			$unitValueClone->save();

			$invoiceline = InvoiceLine::find($line->Id);
			
			$invoiceLineClone = $invoiceline->replicate();

			$invoiceLineClone->invoice_id = $invoiceClone->Id;
			
			$invoiceLineClone->unitValue_id = $unitValueClone->Id;
			
			$invoiceLineClone->save();
			
			foreach($line['taxableItems'] as $taxitem) {
				
				$item = TaxableItem::find($taxitem->Id);

				$itemClone = $item->replicate();

				$itemClone->invoiceline_id = $invoiceLineClone->Id;
				
				$itemClone->save();
			}

		}

		return $invoiceClone;
	}

	public function SetItemsActiveDate($activeFromDate, $activeToDate) {
		$urlbase = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/codetypes/EGS/codes/%s";
		$items = ETAItem::all();
		$this->AuthenticateETA2();

		error_log("updating all items with active date from ".$activeFromDate." to date".$activeToDate);
		foreach($items as $item) {
			$url = sprintf($urlbase, $item->itemCode);
			$response = Http::withToken($this->token)->put($url, [
				'activeFrom' => $activeFromDate,
				'activeTo'	=> $activeToDate,
			]);
			if (array_key_exists("error", $response))
				error_log("updating ".$item->itemCode." failed with error :".$response["error"]["details"][0]["message"]);
			else
				error_log("updating ".$item->itemCode." succeeded!");
		}
	}

	public function pingETA(Request $request) {
		$url = $request['login_url'];
		$response = Http::asForm()->post($url, [
			"grant_type" => "client_credentials",
			"scope" => "InvoicingAPI",
			"client_id" => $request['client_id'],
			"client_secret" => $request['client_secret1'],
		]);
		return ['status' => $response->status(), 'body' => $response->body()];
	}
}
