<?php

namespace App\Http\Middleware;
 
use Closure;
use Session;
use App;
use Config;
 
class SetLocale
{
	public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            $locale = Session::get('locale', Config::get('app.locale'));
        } else {
            $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

            if ($locale != 'ar' && $locale != 'en') {
                $locale = 'ar';
            }
        }
        if ($request->route()->getName() === 'setup.step1' || 
            $request->route()->getName() === 'setup.step2' ||
            $request->route()->getName() === 'setup.step3') {
            $locale = 'ar';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
