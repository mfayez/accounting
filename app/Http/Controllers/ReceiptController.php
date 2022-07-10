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
			$receipt->adjustment = 0.0;
			$receipt->exchangeRate = 1;
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
}
