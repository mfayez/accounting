<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ETAItem;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\TaxableItem;
use App\Models\TaxTotal;
use App\Models\Value;
use App\Models\Discount;
use App\Http\Requests\StoreInvoiceRequest;

class ETAController extends Controller
{
	protected $token = '';
	protected $token_expires_at = null;
	
	public function UploadInvoice(Request $request)
	{
		//$request->validate([
        //    'file' => 'required|file|mimes:csv',
        //]);
    
		$temp = $this->csvToArray($request->file);
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
			$invoice->documentTypeVersion = "0.9";
			$invoice->totalDiscountAmount = 0;
			$invoice->totalSalesAmount = 0;
			$invoice->netAmount = 0;
			$invoice->totalAmount = 0;
			$invoice->extraDiscountAmount = 0;
			$invoice->totalItemsDiscountAmount = 0;	
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
		
		return $temp;
        //$fileName = time().'.'.$request->file->extension();  
     
        //$request->file->move(public_path('file'), $fileName);
  
        /* Store $fileName name in DATABASE from HERE */
        //File::create(['name' => $fileName])
    
	}

	public function AddInvoice(StoreInvoiceRequest $request)
	{
		$url = "https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documentsubmissions";
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
		$invoice = new Invoice($data);
		$invoice->issuer_id = $data['issuer']['Id'];
		$invoice->receiver_id = $data['receiver']['Id'];
		$invoice->save();	
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

	public function SyncReceivedInvoices(Request $request)
	{
	}

	public function SyncIssuedInvoices(Request $request)
	{
		$url = "https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"PageSize" => "10",
			"PageNo" => $request->input("value")
			//,"InvoiceDirection" => "sent"	
		]);
		//$collection = ETAItem::hydrate($response['result']);
		$collection = $response['result'];
		//dd($response);
		dd($response['result']);
		//$collection->transform(function ($item, $key) {
    	//$collection->each(function ($item) {
		foreach($collection as $item) {
			$item2 = new ETAItem($item);
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
		//$collection = $collection->flatten();
		//foreach($response['result'] as $item) {
		//	$dbItem = new ETAItem
		//}
		//dd($collection);
		//DB::transaction (function () use ($collection) {
    	//	$collection->each(function ($item) {
		//		$temp = new ETAItem($item);
		//		$temp->save();
    	//	});
		//});
		return $response['metadata'];
	}

	public function SyncItems(Request $request)
	{
		$url = "https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/my";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"Ps" => "10",
			"Pn" => $request->input("value")
		]);
		//$collection = ETAItem::hydrate($response['result']);
		$collection = $response['result'];
		//$collection->transform(function ($item, $key) {
    	//$collection->each(function ($item) {
		foreach($collection as $item) {
			$item2 = new ETAItem($item);
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
		//$collection = $collection->flatten();
		//foreach($response['result'] as $item) {
		//	$dbItem = new ETAItem
		//}
		//dd($collection);
		//DB::transaction (function () use ($collection) {
    	//	$collection->each(function ($item) {
		//		$temp = new ETAItem($item);
		//		$temp->save();
    	//	});
		//});
		return $response['metadata'];
	}

	public function AddItem(Request $request)
	{
		$url = "https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/codes";
		$data = $request->validate([
			'codeType'		=> ['required', 'string', Rule::in(['EGS', 'GS1'])],
			'parentCode'	=> ['required', 'integer'],
			'itemCode'		=> ['required', 'regex:/EG-[0-9]+-[0-9]+/'],
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

	private function AuthenticateETA(Request $request)
	{
		$this->token = $request->session()->get('eta_token', null);
		$this->token_expires_at = $request->session()->get('eta_token_expires_at', null);
		if ($this->token == null || $this->token_expires_at == null || $this->token_expires_at < Carbon::now()) {
			$url = "https://id.preprod.eta.gov.eg/connect/token";
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

	public function indexIssued()
	{
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('totalDiscountAmount', '=', "{$value}")
                    ->orWhere('netAmount', '=', "{$value}");
            });
        });

		$items = QueryBuilder::for(Invoice::class)
			->with("receiver")
			//->join("Receiver", "Invoice.receiver_id", "Receiver.Id")
			//->join("Issuer", "Invoice.issuer_id", "Issuer.Id")
            ->defaultSort('Invoice.Id')
            ->allowedSorts(['Status'])
            ->allowedFilters(['status', $globalSearch])
            ->paginate(20)
            ->withQueryString();
        return Inertia::render('Invoices/Index', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
				'internalID'	=>	'Internal ID'
			])->addColumns([
                'internalID'	=> 'Internal ID',
				'Status'		=> 'Status',
				'receiver.name' => 'Receiver',
				'receiver.Id'	=> 'Test'
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
    	        if (!$header_en)
        	        $header_en = $row;
    	        else if (!$header_ar)
        	        $header_ar = $row;
            	else
                	$data[] = array_combine($header_en, $row);
        	}
	        fclose($handle);
    	}

    	return $data;
	}
}
