<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ETA\Invoice;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Invoices/Add', []);
    }

    public function search()
    {
        return Inertia::render('Invoices/Search', []);
    }

    public function searchData(Request $request)
    {
        $branchId = $request->input('issuer')['Id'];
        $receiverId = $request->input('receiver')['Id'];
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $status = $request->input('status');
        $size = 1000;
        $offset = ($request->input('page_no') - 1) * $size;

        $sqlstatement = "select 
                            t1.Id as InvID, t1.internalID as Id, month(t1.dateTimeIssued) as Month,
                            CAST(t1.dateTimeIssued as date) as Date, t1.totalAmount as Total,
                            sum(t3.amount) as TaxTotal, t2.name as Client
                        from
                            Invoice t1 inner join Receiver t2 on t2.Id = t1.receiver_id
                            left outer join TaxTotal t3 on t3.invoice_id = t1.Id
                        where
                            (t1.issuer_id = ? or ? = -1)
                            and 
                            (t1.receiver_id = ? or ? = -1) 
                            and 
                            (CAST(t1.dateTimeIssued as date) between ? and ?)
                            and
                            (t1.status = ? or ? = 'all')
                            and
                            (t1.issuer_id in (select issuer_id from user_issuer where user_id = ?))
                        group by
                            t1.internalID, month(t1.dateTimeIssued), CAST(t1.dateTimeIssued as date), t2.name, t1.totalAmount, t1.Id
                        limit ? offset ?";
        $data = DB::select($sqlstatement, [$branchId, $branchId, $receiverId, $receiverId, $startDate, $endDate, $status, $status, 
                Auth::user()->id, $size, 0]);
        return $data;
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = Invoice::with('invoicelines')
            ->with("invoicelines.taxableItems")
            ->with('invoicelines.item')
            ->with('invoicelines.unitValue')
            ->findOrFail($id);
        return Inertia::render('Invoices/Add', [
            'invoice' => $invoice
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
