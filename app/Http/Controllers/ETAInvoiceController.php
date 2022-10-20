<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use ZipArchive;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Http\Traits\ETAAuthenticator;

use App\Models\ETAInvoice;

class ETAInvoiceController extends Controller
{
    use ETAAuthenticator;

    public function downloadPDF(Request $request)
    {
        $url = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0")."/documents/".$request->input("uuid")."/pdf";
		$this->AuthenticateETA($request);
        $response = Http::withToken($this->token)->get($url);
        return response($response->getBody()->getContents(), 200,  [
            'Content-type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename='.$request->input("uuid").'.pdf',
        ]);
    }

    public function branchesPurchases($ids)
    {
        $baseUrl = SETTINGS_VAL("ETA Settings", "eta_url", "https://api.invoicing.eta.gov.eg/api/v1.0");
        $ids = explode(',', $ids);
        $data = ETAInvoice::whereIn('Id', $ids)->pluck('uuid');

        //merging files into one pdf is commented because PDFs downloaded from ETA is encrypted!
        $fileName = time().'.zip';
        $zip = new \ZipArchive();
        $zip->open(storage_path($fileName), \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($data as $uuid) {
            $filePath = storage_path($uuid.'.pdf');
            if (!file_exists($filePath)) {
                $url = $baseUrl."/documents/".$uuid."/pdf";
                $this->AuthenticateETA(Request());
                $response = Http::withToken($this->token)->get($url);
                file_put_contents($filePath, $response->getBody()->getContents());
            }            
            $zip->addFile($filePath, $uuid.'.pdf');
        }
        $zip->close();
        return response()->download(storage_path($fileName));
        /*$pdf = \Webklex\PDFMerger\Facades\PDFMergerFacade::init();
        $tempFiles = array();
        foreach ($data as $uuid) {
            $filePath = storage_path($uuid.'.pdf');
            if (!file_exists($filePath)) {
                $url = $baseUrl."/documents/".$uuid."/pdf";
                $this->AuthenticateETA(Request());
                $response = Http::withToken($this->token)->get($url);
                file_put_contents($filePath, $response->getBody()->getContents());
            }            
            $pdf->addPDF($filePath, 'all');
        }
        $fileName = time().'.pdf';
        $pdf->merge();
        $pdf->save(public_path($fileName));

        return response()->download(public_path($fileName));
        */
    }
}
