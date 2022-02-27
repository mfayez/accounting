<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Meneses\LaravelMpdf\Facades\LaravelMpdf as PDF;

class PDFController extends Controller
{
    public function previewInvoice($id)
    {
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

    public function downloadInvoice(int $id, PDF $pdf)
    {
		$data = Invoice::with('issuer')
            ->with("receiver")
            ->with("invoiceLines")
            ->with("invoiceLines.unitValue")
            ->with("taxTotals")
            ->with("invoiceLines.taxableItems")
            ->with('receiver.address')
            ->find($id);
 		//return view('pdf.saveInvoice', compact('data'));
        return PDF::loadView('pdf.saveInvoice', [
            'data' => $data 
        ])->download("{$id}.pdf");
    }
}
