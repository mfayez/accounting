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

class ETAArchiveController extends Controller
{
    use ETAAuthenticator;

    public function getArchiveRequests(Request $request)
    {
		$url = env("ETA_URL")."/documentPackages/requests";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->get($url, [
			"pageSize" => "20",
			"pageNo" => $request->input("value"),
		]);
        //{"result":[],"metadata":{"totalPages":0,"totalCount":0}}
        return Inertia::render('Archives/Index', [
            'results' => $response['result'],
            'metadata' => $response['metadata']
        ]);
    }

    public function importArchive(Request $request)
    {
        $url = env("ETA_URL")."/documentPackages/".$request->input("rid");
		$this->AuthenticateETA($request);
        //$tmpfile = tempnam(sys_get_temp_dir(), 'xyz') . ".zip";
		$response = Http::withToken($this->token)->get($url);
        //dd(base64_encode($response->getBody()->getContents()));
        //$file = fopen($tmpfile, "w");
        //return $response->getBody()->getContents();
        return response($response->getBody()->getContents(), 200,  [
            'Content-type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="archive-'.$request->input("rid").'.zip"',
        ]);
        //return base64_encode($response->getBody()->getContents());
        //$file = fopen("/tmp/medo.zip", "w");
        //fwrite($file, $response->getBody()->getContents());
        //fclose($file);
        //return response()->download($tmpfile, "archive-".$request->input("rid").".zip");
        $zip = new ZipArchive;
        $res = $zip->open("/tmp/medo.zip");
        if ($res === TRUE) {
            $zip->extractTo('/tmp/');
            $zip->close();
            return "success";
          } else {
              return "error";
          }
        return $response;
    }

    /**
     * Store a newly created resource in ETA.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $url = env("ETA_URL")."/documentPackages/requests";
		$this->AuthenticateETA($request);
		$response = Http::withToken($this->token)->post($url, [
            "type" => "Summary", 
            "format" => "JSON", 
            "queryParameters" => [
                    "dateFrom" => $request->input("startDate"), 
                    "dateTo" => $request->input("endDate"), 
                  "statuses" => [
                     "Valid", 
                     "Cancelled", 
                     "Rejected" 
                  ], 
                  "productsInternalCodes" => [
                     ], 
                  "receiverSenderId" => "", 
                  "receiverSenderType" => "0", 
                  "branchNumber" => "", 
                  "itemCodes" => [], 
                  "documentTypeNames" => [
                              ] 
               ] 
         ]);
        return $response;
    }
}
