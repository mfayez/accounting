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

class ReportsController extends Controller
{
	public function summary()
	{
        return Inertia::render('Reports/Summary', [
        ]);
	}

	public function summaryData(Request $request)
	{
		$branchId   = $request->input('issuer')['Id'];
		$customerId = $request->input('receiver')['Id'];
		$startDate  = $request->input('startDate');
		$endDate    = $request->input('endDate');
		$strSqlStmt1 = "select t1.internalID as Id, month(t1.dateTimeIssued) as Month, date(t1.dateTimeIssued) as Date, 
							sum(t5.amount) as TaxTotal, t4.name as Client, t1.totalAmount as Total
						from Invoice t1 inner join InvoiceLine t2 on t1.Id = t2.invoice_id
							inner join Issuer t3 on t3.Id = t1.issuer_id
							inner join Receiver t4 on t4.Id = t1.receiver_id
						    inner join TaxTotal t5 on t5.invoice_id = t1.Id
						    inner join TaxableItem t6 on t6.invoiceline_id = t2.Id
						where (t1.issuer_id = ? or ? = -1)
							and   (t1.receiver_id = ? or ? = -1)
							and t1.dateTimeIssued between ? and ?
						group by t1.internalID, month(t1.dateTimeIssued), date(t1.dateTimeIssued), t4.name, t1.totalAmount";
		$data = DB::select($strSqlStmt1, [$branchId, $branchId, $customerId, $customerId, $startDate, $endDate]);
		return $data;
	}
	
	public function summaryDownload(Request $request)
	{
		$id = $reeust->input('input');
	}
}
