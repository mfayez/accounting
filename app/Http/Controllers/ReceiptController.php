<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Address;
use App\Models\ETAItem;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\ReceiptTaxTotal;
use App\Models\ReceiptTaxableItem;
use App\Models\Issuer;
use App\Models\Settings;

use App\Http\Traits\ETAAuthenticator;
use App\Http\Traits\ExcelWrapper;

class ReceiptController extends Controller
{
    use ETAAuthenticator;
	use ExcelWrapper;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $columns = $request->query("columns", []);
		$remember = $request->query("remember", "yes");
		if (count($columns) == 0 && $remember == 'yes')
		{
			$columns_str = SETTINGS_VAL(Auth::user()->name, "index.receipts.columns", "[]");
			$columns = json_decode($columns_str);
			if (count($columns) > 0)
				return redirect()->route('eta.receipts.index', ["columns" => $columns]);
		}
		SETTINGS_SET_VAL(Auth::user()->name, "index.receipts.columns", json_encode($columns));
		
		//$myid = Issuer::whereNotNull('issuer_id')->pluck('issuer_id');
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('totalItemsDiscount', '=', "{$value}")
                    ->orWhere('netAmount', '=', "{$value}")
                    ->orWhere('totalSales', '=', "{$value}")
                    ->orWhere('totalAmount', '=', "{$value}")
                    ->orWhere('receiptNumber', '=', "{$value}");
            });
        });

		$items = QueryBuilder::for(Receipt::class)
            ->defaultSort('Id')
			//->whereNotIn('issuerId', $myid)
			->allowedSorts(['status' , 'receiptNumber' , 'totalAmount' , 'netAmount' , 'totalSales' ,
			 		'totalItemsDiscount', 'dateTimeIssued'])
            ->allowedFilters(['status', 'receiptNumber', $globalSearch])
            ->paginate(20)
            ->withQueryString();
        return Inertia::render('Receipts/Index', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
				'internalId'	=>	__('Internal ID'),
				'status'	=>	__('Status')
			])->addColumns([
                'receiptNumber'	=> __('Receipt Number'),
				'buyer_id' => __('Buyer ID'),
				'buyer_name' => __('Buyer Name'),
				'dateTimeIssued' => __('Issued At'),
				'totalSales' => __('Sales'),
				'totalItemsDiscount' => __('Discount'),
				'netAmount' => __('Net'),
				'totalAmount' => __('Total'),
				'status' => __('Status'),
            ]);
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function show(Receipt $receipt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function edit(Receipt $receipt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receipt $receipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receipt $receipt)
    {
        //
    }

    public function UploadReceipts(Request $request)
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

		$inserted = array(); 
		$updated = array(); 
		foreach($temp as $key=>$receipt_data)
		{
			if (isset($inserted[$receipt_data['receiptNumber']])) {
				$temp[$key]["receipt_id"] = $inserted[$receipt_data['receiptNumber']];
				continue;
			}
			$receipt = new Receipt($receipt_data);
			$receipt->currency = "EGP";
			$receipt->adjustment = 0;
			$receipt->exchangeRate = 0;
			$receipt->feesAmount = 0;
			$receipt->status = "In Review";	
			$receipt->statusreason = "Excel Upload";
            $receipt->uuid = "N/A";
            $receipt->previousUUID = "N/A";
            $receipt->totalAmount = 0;
            $receipt->totalCommercialDiscount = 0;
            $receipt->totalItemsDiscount = 0;
            $receipt->totalSales = 0;
			$receipt->save();
			$temp[$key]["receipt_id"] = $receipt->id;
			$inserted[$receipt_data['receiptNumber']] = $receipt->id;
		}
        
		foreach($temp as $key=>$receipt_data)
		{
			$item = ETAItem::where("itemCode", "=", $receipt_data["itemCode"])->first();
			if (!$item){
				$temp[$key]["hasError"] = true;
				$temp[$key]["error"] = __("Item") ." ". $receipt_data["itemCode"]. " ". __("not found!");
				continue;
			}
			$temp[$key]["hasError"] = false;
			
			$receiptItem = new ReceiptItem($receipt_data);
			$receiptItem->receipt_id = $temp[$key]["receipt_id"];
			$receiptItem->itemType = $item->codeTypeName;//"EGS"
			$receiptItem->description = $item->codeNamePrimaryLang;
			$receiptItem->internalCode = $item->Id;
			$receiptItem->netSale = $receipt_data["totalSale"];
			$receiptItem->save();
			
			if ($receipt_data["T1(V009)"] > 0) {
				$item1 = new ReceiptTaxableItem(["taxType" => "T1", "subType" => "V009", "amount" => floatval($receipt_data["T1(V009)"])]);
				$item1->rate = round($item1->amount * 100 / $receiptItem->totalSale, 2);
				$item1->receiptItem_id = $receiptItem->id;
				$item1->save();
			}
			//$receiptItem->receipt->normalize();
            //$receiptItem->receipt->save();
			
		}
		foreach($temp as $key=>$receipt_data)
		{
			if (!isset($updated[$receipt_data['receipt_id']])) {
				$receipt = Receipt::find($receipt_data['receipt_id']);
				$receipt->normalize();
                $receipt->updateTaxTotals();
                $receipt->save();
                $updated[$receipt_data['receipt_id']] = $receipt->id;
			}
		}
		
		return $temp;
	}

    function SignAndSendReceipt(Request $request){
        $data = $request->validate([
            'id' => ['required', 'integer']
        ]);
        $item = Receipt::find($data['id']);
        $lastUUID = null;//'a53030d65802d6baeff04c0efb54d9c273916989d2efa390caf65c2d5fd5d0ab';
        $document = $this->ReceiptToDocument($item, $lastUUID);
        $retValue = $this->fillUUID($document);
        return $retValue;
        $this->AuthenticatePOS($request, $item->seller);
        $result = $this->SendReceiptToETA($retValue);
        return $result;
    }

    public function SendReceiptToETA($data)
	{
		$url = env("ETA_URL")."/receiptsubmissions";
		$response = Http::withToken($this->pos_token)->post($url, ["receipts" => array($data)]);
		return $response;
	}

    function SignAndSendReceipts(Request $request){
        
    }

    function ReceiptToDocument($receipt, $lastUUID){
        $retValue = [];
        //document type
        $retValue['documentType'] = [];
        $retValue['documentType']['receiptType'] = 'S';
        $retValue['documentType']['typeVersion'] = '1.1';
        //seller
        $retValue['seller'] = [];
        $retValue['seller']['branchCode'] = $receipt->seller->issuer->address->branchID;
        $retValue['seller']['rin'] = $receipt->seller->issuer->issuer_id;
        $retValue['seller']['companyTradeName'] = $receipt->seller->issuer->name;
        $retValue['seller']['branchAddress'] = $receipt->seller->issuer->address->toArray();
        $retValue['seller']['branchAddress']['branchID'] = null;
        $retValue['seller']['deviceSerialNumber'] = $receipt->seller->serial;
        $retValue['seller']['syndicateLicenseNumber'] = '0';
        $retValue['seller']['activityCode'] = $receipt->seller->activity_code;
        //header
        $retValue['header'] = [];
        $retValue['header']['dateTimeIssued'] = $receipt->dateTimeIssued->format('Y-m-d\TH:i:s\Z');
        $retValue['header']['receiptNumber'] = $receipt->receiptNumber;
        $retValue['header']['uuid'] = '';
        $retValue['header']['previousUUID'] = $lastUUID;
        $retValue['header']['referenceOldUUID'] = $receipt->referenceOldUUID;
        $retValue['header']['currency'] = $receipt->currency;
        $retValue['header']['exchangeRate'] = null;// $receipt->exchangeRate; //TODO MFayez put it back
        $retValue['header']['sOrderNameCode'] = $receipt->sOrderNameCode;
        $retValue['header']['orderdeliveryMode'] = $receipt->orderdeliveryMode;
        $retValue['header']['grossWeight'] = $receipt->grossWeight;
        $retValue['header']['netWeight'] = $receipt->netWeight;
        //buyer
        $retValue['buyer'] = [];
        $retValue['buyer']['type'] = $receipt->buyer_type;
        $retValue['buyer']['id'] = $receipt->buyer_id;
        $retValue['buyer']['name'] = $receipt->buyer_name;
        $retValue['buyer']['mobileNumber'] = $receipt->buyer_mobileNumber;
        $retValue['buyer']['paymentNumber'] = $receipt->buyer_paymentNumber;
        //Receipt Summary
        $retValue['totalSales'] = $receipt->totalSales;
        $retValue['totalCommercialDiscount'] = $receipt->totalCommercialDiscount;
        $retValue['totalItemsDiscount'] = $receipt->totalItemsDiscount;
        $retValue['netAmount'] = $receipt->netAmount;
        $retValue['feesAmount'] = $receipt->feesAmount;
        $retValue['totalAmount'] = $receipt->totalAmount;
        $retValue['paymentMethod'] = $receipt->paymentMethod;
        $retValue['adjustment'] = $receipt->adjustment;
        //$retValue['extraReceiptDiscountData'] = [];
        //$retValue['extraReceiptDiscountData'][0] = [];
        //$retValue['extraReceiptDiscountData'][0]['amount'] = 0;
        //$retValue['extraReceiptDiscountData'][0]['description'] = "ABC";
        
        //Tax Total
        $retValue['taxTotals'] = [];
        foreach($receipt->taxTotals as $key=>$taxItem){
            $retValue['taxTotals'][$key]['taxType'] = $taxItem->taxType;
            $retValue['taxTotals'][$key]['amount'] = $taxItem->amount;
        }
        //receipt items
        $retValue['itemData'] = [];
        foreach($receipt->receiptItems as $key=>$item){
            $retValue['itemData'][$key] = $this->ReceiptItemToDocument($item);
        }

        $retValue = $this->removeNullValues($retValue);
        $retValue['header']['uuid'] = '';
        if ($lastUUID == null)
            $retValue['header']['previousUUID'] = '';
        
        return $retValue;
    }

    function ReceiptItemToDocument($item)
    {
        $retValue = [];
        $retValue['internalCode'] = $item->internalCode;
        $retValue['description'] = $item->description;
        $retValue['itemType'] = $item->itemType;
        $retValue['itemCode'] = $item->itemCode;
        $retValue['unitType'] = $item->unitType;
        $retValue['quantity'] = $item->quantity;
        $retValue['unitPrice'] = $item->unitPrice;
        $retValue['netSale'] = $item->netSale;
        $retValue['totalSale'] = $item->totalSale;
        $retValue['total'] = $item->total;
        $retValue['valueDifference'] = $item->valueDifference;
        $retValue['taxableItems'] = [];
        foreach($item->taxableItems as $key=>$taxItem){
            $retValue['taxableItems'][$key]['taxType'] = $taxItem->taxType;
            $retValue['taxableItems'][$key]['amount'] = $taxItem->amount;
            $retValue['taxableItems'][$key]['subType'] = $taxItem->subType;
            $retValue['taxableItems'][$key]['rate'] = $taxItem->rate;
        }
        $retValue['commercialDiscountData'] = [];
        //"amount": 867.86000, 
        //"description": "XYZ"
        $retValue['itemDiscountData'] = [];
        //"amount": 10,
        //"description":"ABC"
        
        return $retValue;
    }

    function fillUUID($receipt)
    {
        $canonicalForm = $this->serializeToken($receipt);
        $receipt['header']['uuid'] = hash("sha256", $canonicalForm);
        
        return $receipt;
    }

    function serializeToken($request)
    {
        $serialized = "";

        if ($this->is_assoc($request))
        {
            foreach($request as $key=>$val)
            {
                $serialized .= "\"" . strtoupper($key) . "\"";
                //Value is zero-indexed array
                if (is_bool($val) || is_int($val) || is_float($val))
                    $serialized .= "\"" . $val . "\"";
                else if ($val instanceof Carbon)
                    $serialized .= "\"" . $val->format('Y-m-d\TH:i:s\Z') . "\"";
                else if (is_string($val))
                    $serialized .= "\"" . $val . "\"";
                else if ($this->is_assoc($val))
                    $serialized .= $this->SerializeToken($val);
                else
                {
                    foreach($val as $item){
                        $serialized .= "\"" . strtoupper($key) . "\"";
                        $serialized .= $this->SerializeToken($item);
                    }
                }   
            }
        }
        else {
            //TODO MFayez put it here to be ready, but current ETA object do not have such type
        }
        
        return $serialized;
    }

    function removeNullValues(array $data)
    {
        $filtered_data = [];
        foreach ($data as $key => $value) {
            if (is_array($value))
            {
                if (sizeof($value) > 0)
                    $filtered_data[$key] = $this->removeNullValues($value);
            }
            else if ($value != null){
                $filtered_data[$key] = $value;
            }
        }

        return $filtered_data;
    }

    function is_assoc($array){
        if (!is_array($array))
            return False;
        $keys = array_keys($array);
        return $keys !== array_keys($keys);
     }

     function is_date($item)
     {

     }
}
