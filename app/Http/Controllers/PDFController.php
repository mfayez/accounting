<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

class PDFController extends Controller
{
	function previewInvoice($id){
		$data = Invoice::with('issuer')
			->with("receiver")
			->with("invoiceLines")
			->with("invoiceLines.unitValue")
			->with("taxTotals")
			->with("invoiceLines.taxableItems")
			->with('receiver.address')
			->find($id);
		return view('pdf.bill', compact('data'));
	}
}
