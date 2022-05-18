<?php

namespace App\Http\Controllers;

use App\Exports\ReportSummaryExport;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
	private $mValue;
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
						    left outer join TaxTotal t5 on t5.invoice_id = t1.Id
						    left outer join TaxableItem t6 on t6.invoiceline_id = t2.Id
						where (t1.issuer_id = ? or ? = -1)
							and   (t1.receiver_id = ? or ? = -1)
							and t1.dateTimeIssued between ? and DATE_ADD(?, INTERVAL 1 DAY) and t1.status = 'Valid'
						group by t1.internalID, month(t1.dateTimeIssued), date(t1.dateTimeIssued), t4.name, t1.totalAmount";
		$data = DB::select($strSqlStmt1, [$branchId, $branchId, $customerId, $customerId, $startDate, $endDate]);
		return $data;
	}
	
	public function summaryDownload(Request $request)
	{
		$branchId   = $request->input('issuer')['Id'];
		$customerId = $request->input('receiver')['Id'];
		$startDate  = $request->input('startDate');
		$endDate    = $request->input('endDate');
		//$branchId   = -1;
		//$customerId = -1;
		//$startDate  = "2019-10-10";
		//$endDate    = "2030-10-10";
		$strSqlStmt1 = "select t1.Id as InvKey, t1.internalID as Id, month(t1.dateTimeIssued) as Month, date(t1.dateTimeIssued) as Date, 
							sum(t5.amount) as TaxTotal, t4.name as Client, t1.totalAmount as Total, t4.code as Code
						from Invoice t1  
							inner join Receiver t4 on t4.Id = t1.receiver_id
						    left outer join TaxTotal t5 on t5.invoice_id = t1.Id
						where (t1.issuer_id = ? or ? = -1)
							and   (t1.receiver_id = ? or ? = -1)
							and t1.dateTimeIssued between ? and DATE_ADD(?, INTERVAL 1 DAY) and t1.status = 'Valid'
						group by t1.Id, t1.internalID, month(t1.dateTimeIssued), date(t1.dateTimeIssued), t4.name, t1.totalAmount, t4.code";
		$data1 = DB::select($strSqlStmt1, [$branchId, $branchId, $customerId, $customerId, $startDate, $endDate]);
		$strSqlStmt2 = "select t1.Id as InvKey, t2.description as 'Desc', t2.itemCode as Code, round(sum(t2.quantity), 2) as Quantity,
							round(sum(t2.salesTotal), 2) as Total, round(sum(t7.amountEGP), 2) as UnitValue
						from Invoice t1 inner join InvoiceLine t2 on t1.Id = t2.invoice_id
						    inner join Value t7 on t7.Id = t2.unitValue_id
						where (t1.issuer_id = ? or ? = -1)
							and   (t1.receiver_id = ? or ? = -1)
							and t1.dateTimeIssued between ? and DATE_ADD(?, INTERVAL 1 DAY)
						group by t1.Id, t2.description, t2.itemCode";
		$data2 = DB::select($strSqlStmt2, [$branchId, $branchId, $customerId, $customerId, $startDate, $endDate]);
		$items = array();
		foreach($data2 as $invLine)
			array_push($items, $invLine->Code);
		$items = array_unique($items);
		foreach($data1 as $key=>$val)
		{
			$this->mValue = $val->InvKey;
			$invLines = array_filter($data2, function($v, $k) {
							    return  $v->InvKey == $this->mValue;
						}, ARRAY_FILTER_USE_BOTH);
			$data1[$key]->lines = array();
			foreach($invLines as $invLine)
				$data1[$key]->lines[$invLine->Code] = $invLine;

		}
		//render excel file now
		$reader = IOFactory::createReader('Xlsx');
		$file = $reader->load('./ExcelTemplates/SalesReport.xlsx');
		$colIdx = 9;
		foreach($items as $col){
			$file->getActiveSheet()->setCellValue($this->index($colIdx,1), $col);
			$colIdx += 3;
		}
		$rowIdx = 5;
		foreach($data1 as $row){
			$file->getActiveSheet()->setCellValue($this->index(2,$rowIdx), $row->Id);
			$file->getActiveSheet()->setCellValue($this->index(3,$rowIdx), $row->Month);
			$file->getActiveSheet()->setCellValue($this->index(4,$rowIdx), $row->Date);
			$file->getActiveSheet()->setCellValue($this->index(5,$rowIdx), $row->TaxTotal);
			$file->getActiveSheet()->setCellValue($this->index(6,$rowIdx), $row->Code);
			$file->getActiveSheet()->setCellValue($this->index(7,$rowIdx), $row->Client);
			$file->getActiveSheet()->setCellValue($this->index(8,$rowIdx), $row->Total);
			$colIdx = 9;
			foreach($items as $col){
				if (array_key_exists($col, $row->lines)){
					$file->getActiveSheet()->setCellValue($this->index($colIdx,2), $row->lines[$col]->Desc);
					$file->getActiveSheet()->setCellValue($this->index($colIdx+0,$rowIdx), $row->lines[$col]->Total);
					$file->getActiveSheet()->setCellValue($this->index($colIdx+1,$rowIdx), $row->lines[$col]->Quantity);
					$file->getActiveSheet()->setCellValue($this->index($colIdx+2,$rowIdx), $row->lines[$col]->UnitValue);
				}
				$colIdx += 3;
			}
			$rowIdx++;
		}
		$writer = IOFactory::createWriter($file, 'Xlsx');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Sales_ExportedData.xls"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//$writer->save('./ExcelTemplates/SalesReport2.xlsx');
		//return $data1;
	}

	public function purchase()
	{
        return Inertia::render('Reports/Purchase', [
        ]);
	}

	public function purchaseData(Request $request)
	{
		$startDate  = $request->input('startDate');
		$endDate    = $request->input('endDate');
		$strSqlStmt1 = "select t1.internalID as Id, month(t1.dateTimeIssued) as Month, date(t1.dateTimeIssued) as Date, 
							t1.issuerName as Seller, t1.issuerId as SellerTaxId, t1.totalSales as Sales, t1.netAmount as Net, t1.total as Total
						from ETAInvoices t1 
						where t1.dateTimeIssued between ? and DATE_ADD(?, INTERVAL 1 DAY) and t1.status = 'Valid'";
		$data = DB::select($strSqlStmt1, [$startDate, $endDate]);
		return $data;
	}
	
	public function purchaseDownload(Request $request)
	{
		$startDate  = $request->input('startDate');
		$endDate    = $request->input('endDate');
		$strSqlStmt1 = "select t1.internalID as Id, month(t1.dateTimeIssued) as Month, date(t1.dateTimeIssued) as Date, 
							t1.issuerName as Seller, t1.issuerId as SellerTaxId, t1.totalSales as Sales, t1.netAmount as Net, t1.total as Total
						from ETAInvoices t1 
						where t1.dateTimeIssued between ? and DATE_ADD(?, INTERVAL 1 DAY) and t1.status = 'Valid'";
		$data = DB::select($strSqlStmt1, [$startDate, $endDate]);
		
		//render excel file now
		$reader = IOFactory::createReader('Xlsx');
		$file = $reader->load('./ExcelTemplates/PurchaseReport.xlsx');
		$rowIdx = 4;
		foreach($data as $row){
			$file->getActiveSheet()->setCellValue($this->index(2,$rowIdx), $row->Id);
			$file->getActiveSheet()->setCellValue($this->index(3,$rowIdx), $row->Month);
			$file->getActiveSheet()->setCellValue($this->index(4,$rowIdx), $row->Date);
			$file->getActiveSheet()->setCellValue($this->index(5,$rowIdx), round($row->Total - $row->Net, 2));
			$file->getActiveSheet()->setCellValue($this->index(6,$rowIdx), $row->SellerTaxId);
			$file->getActiveSheet()->setCellValue($this->index(7,$rowIdx), $row->Seller);
			$file->getActiveSheet()->setCellValue($this->index(8,$rowIdx), $row->Total);
			$rowIdx++;
		}
		$writer = IOFactory::createWriter($file, 'Xlsx');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Purchase_ExportedData.xls"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//$writer->save('./ExcelTemplates/SalesReport2.xlsx');
		//return $data1;
	}

	public function summaryOnlyData(Request $request) {

		return Excel::download(new ReportSummaryExport($this->summaryData($request)) , 'Report.xlsx');

	}


	private function index($col, $row)
	{
		$col1 = Coordinate::stringFromColumnIndex($col);
		return $col1.$row;
	}
}
