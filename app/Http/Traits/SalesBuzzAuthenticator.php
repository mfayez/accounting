<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use CasperBiering\Dotnet\BinaryXml\Decoder;
use CasperBiering\Dotnet\BinaryXml\Encoder;


trait SalesBuzzAuthenticator {
	protected $salezbuzz_cookies = "";
	protected $salezbuzz_headers = [];

    private function AuthenticateSB($request, $username, $password, $buid, $url)
	{
		//check if $request session has salesbuzz token
		if ($request && $request->session()->has("salesbuzz_token") && $request->session()->get("salesbuzz_token") != ""){
			$this->salezbuzz_cookies = $request->session()->get("salesbuzz_token");
			$this->salezbuzz_headers = [
				'Cookie' => $this->salezbuzz_cookies,
				'Content-Type' => 'application/msbin1',
			];
			return;
		}
		$url = "$url/ClientBin/BI-SalesBuzz-BackOffice-Web-AuthenticationService.svc/binary/Login";
       /*$response = Http::post($url, [
            "userName"=>"$username:$buid:ar-EG",
            "password"=> $password,
            "WindowsUserName"=>"",
            "ADAuthenticationLogin"=>"0",
            //"BUID" => "11102",
        ]);*/
		//this may be used with binary urls
		$encoder = new Encoder();
		$response = Http::contentType("application/msbin1")->send('POST', $url, [
			'body' => $encoder->encode("<Login xmlns=\"http://tempuri.org/\"><userName>$username:$buid:en-US</userName><password>$password</password><isPersistent>false</isPersistent><customData i:nil=\"true\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\"></customData></Login>")
		]);
		
        $cookies = collect($response->cookies->toArray())->keyBy('Name')->map->Value;
        $cookieString = $cookies->map(function($value, $key){
            return $key . '=' . $value;
        })->implode('; ');
		$this->salezbuzz_cookies = $cookieString;
		$this->salezbuzz_headers = [
			'Cookie' => $this->salezbuzz_cookies,
			'Content-Type' => 'application/msbin1',
		];
		if ($request)
			$request->session()->put("salesbuzz_token", $cookieString);
	}
}