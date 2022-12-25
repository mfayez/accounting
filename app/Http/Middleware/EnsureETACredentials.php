<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureETACredentials
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
        if ($request->route()->getName() === 'setup.step1' || 
            $request->route()->getName() === 'setup.step2' ||
            $request->route()->getName() === 'setup.step3') {
        }
        
        $eta_client_id = SETTINGS_VAL('ETA Settings', 'client_id', '');
        $eta_client_secret1 = SETTINGS_VAL('ETA Settings', 'client_secret1', '');
        $eta_client_secret2 = SETTINGS_VAL('ETA Settings', 'client_secret2', '');
        //check if client id and secret are set
        if (empty($eta_client_id) || empty($eta_client_secret1) || empty($eta_client_secret2)) {

            return redirect()
                ->route('setup.step1')
                ->with('error', 'ETA Client ID and Secret are not set. Please set them in Settings.');
        }

        //check company settings
        $company_name = SETTINGS_VAL('Company Settings', 'company_name', '');
        $company_taxid = SETTINGS_VAL('Company Settings', 'tax_number', '');
        if (empty($company_name) || empty($company_taxid)) {
            return redirect()
                ->route('setup.step2')
                ->with('error', 'Company name and taxid are not set. Please set them in Settings.');
        }
        
        return $next($request);
    }
}
