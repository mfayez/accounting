<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use ZipArchive;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

use App\Http\Traits\ETAAuthenticator;

class ETAInvoiceController extends Controller
{
    use ETAAuthenticator;

    public function downloadPDF(Request $request)
    {
        $url = env("ETA_URL")."/documents/".$request->input("uuid")."/pdf";
		$this->AuthenticateETA($request);
        $response = Http::withToken($this->token)->get($url);
        return response($response->getBody()->getContents(), 200,  [
            'Content-type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename='.$request->input("uuid").'.pdf',
        ]);
    }
}
