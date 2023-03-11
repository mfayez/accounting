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
            $table->column(
                key: 'internalId',
                label: __('Internal ID'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'issuerName',
                label: __('Issuer'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'receiverId',
                label: __('Receiver Registration Number'),
                canBeHidden: true,
                hidden: false,
                sortable: false
            )->column(
                key: 'branch.name',
                label: __('Receiving Branch'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'dateTimeIssued',
                label: __('Issued At'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'dateTimeReceived',
                label: __('Received At'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'totalSales',
                label: __('Sales'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'totalDiscount',
                label: __('Discount'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'netAmount',
                label: __('Net'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'total',
                label: __('Total'),
                canBeHidden: true,
                hidden: false,
                sortable: true
            )->column(
                key: 'actions',
                label: __('Actions')
            )->searchInput(
                key: 'internalId',
                label: __('Internal ID')
            )->searchInput(
                key: 'status',
                label: __('Status')
            )->searchInput(
                key: 'receiver_id',
                label: __('Receiving Branch')
            );
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
