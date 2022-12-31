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
use App\Models\General\Address;
use App\Models\ETA\ETAItem;
use App\Models\ETA\ETAInvoice;
use App\Models\ETA\Invoice;
use App\Models\ETA\InvoiceLine;
use App\Models\ETA\TaxableItem;
use App\Models\ETA\TaxTotal;
use App\Models\ETA\Value;
use App\Models\ETA\Discount;
use App\Models\ETA\Receiver;
use App\Models\ETA\Issuer;
use App\Models\General\Upload;
use App\Models\General\Settings;

use App\Http\Traits\ETAAuthenticator;
use App\Http\Traits\ExcelWrapper;

class ReceivedInvoiceController extends Controller
{
    public function indexInvoices(Request $request)
	{
		$columns = $request->query("columns", []);
		$remember = $request->query("remember", "yes");
		if (count($columns) == 0 && $remember == 'yes')
		{
			$columns_str = SETTINGS_VAL(Auth::user()->name, "index.received.columns", "[]");
			$columns = json_decode($columns_str);
			if (count($columns) > 0)
				return redirect()->route('eta.invoices.received.index', ["columns" => $columns]);
		}
		SETTINGS_SET_VAL(Auth::user()->name, "index.received.columns", json_encode($columns));
		
		$myid = Issuer::whereNotNull('issuer_id')->pluck('issuer_id');
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('totalDiscount', '=', "{$value}")
                    ->orWhere('netAmount', '=', "{$value}")
                    ->orWhere('internalID', '=', "{$value}");
            });
        });

		$items = QueryBuilder::for(ETAInvoice::class)
            ->defaultSort('Id')
			->whereNotIn('issuerId', $myid)
			->with('branch')
			->allowedSorts(['status' , 'internalId' , 'total' , 'netAmount' , 'totalSales' ,
			 		'totalDiscount', 'dateTimeIssued', 'dateTimeReceived'])
            ->allowedFilters(['status', 'internalId', 'receiver_id', $globalSearch])
            ->paginate(20)
            ->withQueryString();
        return Inertia::render('Invoices/IndexReceived', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
				'internalId'	=>	__('Internal ID'),
				'status'		=>	__('Status'),
				'receiver_id'	=>	__('Receiving Branch')
			])->addColumns([
                'internalId'	=> __('Internal ID'),
				'issuerName' => __('Issuer'),
				'receiverId' => __('Receiver Registration Number'),
				'branch.name' => __('Receiving Branch'),
				'dateTimeIssued' => __('Issued At'),
				'dateTimeReceived' => __('Received At'),
				'totalSales' => __('Sales'),
				'totalDiscount' => __('Discount'),
				'netAmount' => __('Net'),
				'total' => __('Total'),
				'status' => __('Status'),
            ]);
        });
    }

	public function updateDetails(Request $request)
	{
		$inv2 = ETAInvoice::where("id", "=", $request->input("id"))->first();
		if($inv2){
			$inv2->receiver_id = $request->input("issuer")["Id"];
			$inv2->comment = $request->input("comment");
			$inv2->reviewer = Auth::user()->name;
			$inv2->save();
			return "updated!";
		}
		
		return "Invoice not found!";
	}
	
}
