<?php

namespace App\Http\Controllers;

use App\Models\ETA\Invoice;
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

    public function previewInvoices($ids)
    {
        $ids = explode(',', $ids);
        $invoices = Invoice::with('issuer')
            ->with("receiver")
            ->with("invoiceLines")
            ->with("invoiceLines.unitValue")
            ->with("taxTotals")
            ->with("invoiceLines.taxableItems")
            ->with('receiver.address')
            ->whereIn('id', $ids)
            ->get();
        return view('pdf.bills', compact('invoices'));
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
