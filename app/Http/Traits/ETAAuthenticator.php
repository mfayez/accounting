<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait ETAAuthenticator {
    protected $token = '';
	protected $token_expires_at = null;

    private function AuthenticateETA2()
	{
		if ($this->token == null || $this->token_expires_at == null || $this->token_expires_at < Carbon::now()) {
			$url = env("LOGIN_URL");
			$response = Http::asForm()->post($url, [
				"grant_type" => "client_credentials",
				"scope" => "InvoicingAPI",
				"client_id" => env("CLIENT_ID"),
				"client_secret" => env("CLIENT_SECRET") 
			]);
			$this->token = $response['access_token'];
			$this->token_expires_at = Carbon::now()->addSeconds($response['expires_in']-10);
		}
	}

	private function AuthenticateETA(Request $request)
	{
		$this->token = $request->session()->get('eta_token', null);
		$this->token_expires_at = $request->session()->get('eta_token_expires_at', null);
		if ($this->token == null || $this->token_expires_at == null || $this->token_expires_at < Carbon::now()) {
			$url = env("LOGIN_URL");
			$response = Http::asForm()->post($url, [
				"grant_type" => "client_credentials",
				"scope" => "InvoicingAPI",
				"client_id" => env("CLIENT_ID"),
				"client_secret" => env("CLIENT_SECRET") 
			]);
			$this->token = $response['access_token'];
			$this->token_expires_at = Carbon::now()->addSeconds($response['expires_in']-10);
			$request->session()->put('eta_token', $this->token);
			$request->session()->put('eta_token_expires_at', $this->token_expires_at);
			//$request->session()->flash('status', 'Task was successful!');
		}
	}

	private function AuthenticatePOS($request, $pos)
	{
		$this->pos_token = $request->session()->get('pos_eta_token', null);
		$this->pos_token_expires_at = $request->session()->get('pos_eta_token_expires_at', null);
		if ($this->pos_token == null || $this->pos_token_expires_at == null || $this->pos_token_expires_at < Carbon::now()) {
			$url = env("LOGIN_URL");
			$response = Http::withHeaders([
				'posserial' => $pos->serial,
				'pososversion' => $pos->os_version,
				'posmodelframework' => $pos->model,
				'presharedkey' => ''
			])->asForm()->post($url, [
				"grant_type" => "client_credentials",
				"client_id" => env("CLIENT_ID"),
				"client_secret" => env("CLIENT_SECRET"),
			]);
			$this->pos_token = $response['access_token'];
			$this->pos_token_expires_at = Carbon::now()->addSeconds($response['expires_in']-10);
			$request->session()->put('pos_eta_token', $this->token);
			$request->session()->put('pos_eta_token_expires_at', $this->token_expires_at);
			//$request->session()->flash('status', 'Task was successful!');
		}
	}
}