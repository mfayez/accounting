<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait SalesBuzzAuthenticator {
	protected $salezbuzz_cookies = "";
	protected $salezbuzz_headers = [];

    private function AuthenticateSB(Request $request, $username, $password, $buid)
	{
		//check if $request session has salesbuzz token
		if ($request && $request->session()->has("salesbuzz_token") && $request->session()->get("salesbuzz_token") != ""){
			$this->salezbuzz_cookies = $request->session()->get("salesbuzz_token");
			$this->salezbuzz_headers = [
				'Cookie' => $this->salezbuzz_cookies
			];
			return;
		}
		$url = "https://sb.hkdist.com/salesbuzzbo/ClientBin/BI-SalesBuzz-BackOffice-Web-AuthenticationService.svc/json/Login";
        $response = Http::post($url, [
            "userName"=>"$username:$buid:ar-EG",
            "password"=> $password,
            "WindowsUserName"=>"",
            "ADAuthenticationLogin"=>"0",
            //"BUID" => "11102",
        ]);
        $cookies = collect($response->cookies->toArray())->keyBy('Name')->map->Value;
        $cookieString = $cookies->map(function($value, $key){
            return $key . '=' . $value;
        })->implode('; ');
		$this->salezbuzz_cookies = $cookieString;
		$this->salezbuzz_headers = [
			'Cookie' => $this->salezbuzz_cookies
		];
		if ($request)
			$request->session()->put("salesbuzz_token", $cookieString);
	}
}