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
use App\Http\Requests\StoreInvoiceRequest;

class ETAController extends Controller
{

	public function generateInvoiceNumber($invoice){
		$values = array("YYYY", "YY", "BB", "XXXXXXX", "XXXXXX", "XXXXX", "XXXX");
		$repalcements = array("%1$04d", "%2$02d", "%3$02d", "%4$07d", "%4$06d", "%4$05d", "%4$04d");
		$template = str_replace($values, $repalcements, env("INVOICE_TEMPALTE"));
		$branchNum = $invoice->issuer_id;
		$inv = DB::select('SELECT max(convert(internalID, integer)) as LastInv FROM `Invoice` WHERE issuer_id', [$branchNum]);
		$invNum = 1;
		if (!empty($inv))
			$invNum = 1 + $inv[0]->LastInv%100000;
		$year = intval(date("Y"));
		$year2 = $year % 100;
		$invoice->internalID = sprintf($template, $year, $year2, $branchNum, $invNum);
	}
	protected $token = '';
	protected $token_expires_at = null;
	
	public function UploadItem(Request $request)
	{
		$url = env("ETA_URL")."/codetypes/requests/codes";
		$temp = $this->csvToArray($request->file);
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->post($url, ["items" => temp]);
		return $response;
		
	}
	public function UploadInvoice(Request $request)
	{
		//$request->validate([
        //    'file' => 'required|file|mimes:csv',
        //]);
    
		$temp = $this->csvToArray($request->file);
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
			$invoice->documentTypeVersion = "1.0";
			$invoice->totalDiscountAmount = 0;
			$invoice->totalSalesAmount = 0;
			$invoice->netAmount = 0;
			$invoice->totalAmount = 0;
			$invoice->extraDiscountAmount = 0;
			$invoice->totalItemsDiscountAmount = 0;
			$invoice->status = "In Review";	
			$invoice->statusreason = "Excel Upload";
			$invoice->upload_id = $upload->Id;
			$this->generateInvoiceNumber($invoice);
			$invoice->save();
			$temp[$key]["invoice_id"] = $invoice->Id;
			$inserted[$invoice_data['internalID']] = $invoice->Id;
		}
		foreach($temp as $key=>$invoice_data)
		{
			$item = ETAItem::where("itemCode", "=", $invoice_data["itemCode"])->first();
			if (!$item){
				$temp[$key]["error"] = "Item not found!";
				continue;
			}
			
			$unitValue = new Value($invoice_data);
			$unitValue->save();
			$invoiceline = new InvoiceLine($invoice_data);
			$invoiceline->unitValue_id = $unitValue->Id;
			$invoiceline->itemType = $item->codeTypeName;//"EGS"
			$invoiceline->description = $item->codeNamePrimaryLang;
			$invoiceline->internalCode = $item->Id;
			$invoiceline->netTotal = $invoice_data["salesTotal"];
			$invoiceline->valueDifference = 0;
			$invoiceline->totalTaxableFees = 0;
			$invoiceline->itemsDiscount = 0;
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
				$item1->rate = $item1->amount * 100 / $invoiceline->salesTotal;
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
		return $upload;
        //$fileName = time().'.'.$request->file->extension();  
     
        //$request->file->move(public_path('file'), $fileName);
  
        /* Store $fileName name in DATABASE from HERE */
        //File::create(['name' => $fileName])
    
	}

	public function AddInvoice(StoreInvoiceRequest $request)
	{
		$url = env("ETA_URL")."/documentsubmissions";
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
		if ($request->isMethod('post')) {
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
		$url = env("ETA_URL")."/documents/state/%s/state";
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
			return "request accepted";
		}
		
		return "request rejected by ETA";
	}

	public function SyncReceivedInvoices(Request $request)
	{
	}

	public function SyncIssuedInvoices(Request $request)
	{
		$url = env("ETA_URL")."/documents/recent";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"PageSize" => "10",
			"PageNo" => $request->input("value")
			,"InvoiceDirection" => "sent"	
		]);
		//$collection = ETAItem::hydrate($response['result']);
		$collection = $response['result'];
		//$collection->transform(function ($item, $key) {
    	//$collection->each(function ($item) {
		foreach($collection as $item) {
			try{
				//todo mfayez do not mix issued with received invoices in the same table.
				//$invoice = ETAInvoice::updateOrCreate(['uuid' => $item['uuid']], $item); 
				//$invoice = ETAInvoice::firstWhere(['uuid' => $item['uuid']]);
				//if ($invoice)
				//{
				//	$invoice->update($item);
				//	$invoice->save();
				//}
				//$invoice2 = Invoice::firstWhere(['uuid' => $item['uuid'], 'status' => 'processing']);
				$invoice2 = Invoice::firstWhere(['uuid' => $item['uuid']]);
				if ($invoice2)
				{
					$invoice2->status = $item['status'];
					$invoice2->statusreason = $item['documentStatusReason'];
					$invoice2->save();
				} else {
					//recover missing item
					$this->AddMissingInvoice($request, $item['uuid']);
				}
			} catch (Exception $e) {}

			//$invoice->save();
		};
		return $response['metadata'];
	}

	public function SyncItems(Request $request)
	{
		$url = env("ETA_URL")."/codetypes/requests/my";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"Ps" => "10",
			"Pn" => $request->input("value")
		]);
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
		$url = env("ETA_URL")."/codetypes/requests/codes";
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

	private function AuthenticateETA2()
	{
		if ($this->token == null || $this->token_expires_at == null || $this->token_expires_at < Carbon::now()) {
			$url = env("LOGIN_URL");
			$response = Http::asForm()->post($url, [
				"grant_type" => "client_credentials",
				"scope" => "InvoicingAPI",
				"client_id" => env("CLIENT_ID"),
				"client_secret" => env("CLIENT_SECRET") 
			]);
			$this->token = $response['access_token'];
			$this->token_expires_at = Carbon::now()->addSeconds($response['expires_in']-10);
		}
	}

	private function AuthenticateETA(Request $request)
	{
		$this->token = $request->session()->get('eta_token', null);
		$this->token_expires_at = $request->session()->get('eta_token_expires_at', null);
		if ($this->token == null || $this->token_expires_at == null || $this->token_expires_at < Carbon::now()) {
			$url = env("LOGIN_URL");
			$response = Http::asForm()->post($url, [
				"grant_type" => "client_credentials",
				"scope" => "InvoicingAPI",
				"client_id" => env("CLIENT_ID"),
				"client_secret" => env("CLIENT_SECRET") 
			]);
			$this->token = $response['access_token'];
			$this->token_expires_at = Carbon::now()->addSeconds($response['expires_in']-10);
			$request->session()->put('eta_token', $this->token);
			$request->session()->put('eta_token_expires_at', $this->token_expires_at);
			$request->session()->flash('status', 'Task was successful!');
		}
		else {
		}
	}

	public function indexInvoices()
	{
		$myid = Issuer::whereNotNull('issuer_id')->pluck('issuer_id');
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('totalDiscountAmount', '=', "{$value}")
                    ->orWhere('netAmount', '=', "{$value}")
                    ->orWhere('internalID', '=', "{$value}");
            });
        });

		$items = QueryBuilder::for(ETAInvoice::class)
            ->defaultSort('Id')
			->whereNotIn('issuerId', $myid)
            ->allowedSorts(['Status'])
            ->allowedFilters(['status', 'internalID', $globalSearch])
            ->paginate(20)
            ->withQueryString();
        return Inertia::render('Invoices/Index', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
				'internalId'	=>	__('Internal ID'),
				'status'	=>	__('Status')
			])->addColumns([
                'internalId'	=> __('Internal ID'),
				'issuerName' => __('Issuer'),
				'receiverId' => __('Receiver Registration Number'),
				'receiverName' => __('Receiver'),
			#	'dateTimeIssued' => 'Issued At',
			#	'dateTimeReceived' => 'Received At',
				'totalSales' => __('Sales'),
				'totalDiscount' => __('Discount'),
				'netAmount' => __('Net'),
				'total' => __('Total'),
				'status' => __('Status'),
            ]);
        });
    }

	public function indexIssued(Request $request, $upload_id = null)
	{
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
			->whereNotNull('issuer_id')
			->where(function ($query) use ($upload_id){
				if ($upload_id)
            		$query->where('upload_id', '=', $upload_id);
           	})
			//->join("Receiver", "Invoice.receiver_id", "Receiver.Id")
			//->join("Issuer", "Invoice.issuer_id", "Issuer.Id")
            ->defaultSort('-Invoice.Id')
            ->allowedSorts(['Status'])
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
                'codeTypeName'	=>	'Type'	,
				'codeID'	=>	'ETA Code'	,
				'itemCode'	=>	'Standard Code'	,
				'codeNamePrimaryLang'	=>	'Name 1'	,
				'codeNameSecondaryLang'	=>	'Name 2'	,
				'descriptionPrimaryLang'	=>	'Description'	,
				'descriptionSecondaryLang'	=>	'Description'	,
				'parentCodeID'	=>	'ETA Parent Code'	,
				'parentItemCode'	=>	'Standard Parent Code'	,
				'parentCodeNamePrimaryLang'	=>	'Parent Name 1'	,
				'parentCodeNameSecondaryLang'	=>	'Parent Name 2'	,
				'parentLevelName'	=>	'Parent Level Name'	,
				'levelName'	=>	'Level Name'	,
				'requestCreationDateTimeUtc'	=>	'Request Time'	,
				'codeCreationDateTimeUtc'	=>	'Creation Time'	,
				'activeFrom'	=>	'Active Date'	,
				'activeTo'	=>	'Expire Date'	,
				'active'	=>	'Active Status'	,
				'status'	=>	'Status'	,
				'statusReason'	=>	'ETA Comments'	,
            ]);
        });
    }

    public function indexItems_json()
    {
		return ETAItem::all()->toArray();
	}
	
	private function csvToArray($filename = '', $delimiter = ',')
	{
    	if (!file_exists($filename) || !is_readable($filename))
        	return false;

	    $header_en = null;
	    $header_ar = null;
    	$data = array();
	    if (($handle = fopen($filename, 'r')) !== false)
    	{
        	while (($row = fgetcsv($handle, 10000, $delimiter)) !== false)
	        {
    	        if (!$header_en){
					foreach($row as $key=>$item){
						$row[$key] = iconv('UTF-8', 'ASCII//TRANSLIT', $item);	
					}
					$header_en = $row;
				}
    	        else if (!$header_ar)
        	        $header_ar = $row;
            	else
                	$data[] = array_combine($header_en, $row);
        	}
	        fclose($handle);
    	}

    	return $data;
	}

	public function UpdateInvoices(){
		$urlbase = env("ETA_URL")."/documents/%s/raw";
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

	public function AddMissingInvoice($request, $uuid) {
		$urlbase = env("ETA_URL")."/documents/%s/raw";
		$this->AuthenticateETA($request);
		$url = sprintf($urlbase, $uuid);
		$response = Http::withToken($this->token)->get($url);
		$document = json_decode($response['document']);
		error_log($response['uuid']);
		$invoice = new Invoice((array)$document);
		if ($invoice->status != 'Valid') return;

		$issuer = Issuer::where('issuer_id', '=', $document->issuer->id)->first();
		$receiver = Receiver::where('name', '=', $document->receiver->name)->first();
		//$receiver = Receiver::where('receiver_id', '=', $document->receiver->id)->first();
		$invoice->status = $response['status'];
		$invoice->uuid = $response["uuid"];
		$invoice->submissionUUID = $response["submissionUUID"];
		$invoice->longId = $response["longId"];
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
			$item->receiver_id = $document->issuer->id;
			$item->id = null;
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
		$urlbase = env("ETA_URL")."/documents/%s/raw";
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

}
