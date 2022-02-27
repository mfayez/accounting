<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		$this->bootInertia();
		Validator::excludeUnvalidatedArrayKeys();
    }
	
	 /**
     * Initialize inertia js
     */
    private function bootInertia() : void
    {
        // Share the translations data in the props of the components.
        Inertia::share([
		'preview_url' => env("PREVIEW_URL", "https://invoicing.eta.gov.eg/print/documents/"),
		'auto_inv_num' => SETTINGS_VAL('application settings', 'automatic', '0') == '1' ? true : false,
        'locale' => function () {
          return Session()->get('locale', app()->getLocale());
        },
    		'language' => function () {
        		return translations(
            				resource_path('lang/'. Session()->get('locale', app()->getLocale()) .'.json')
        		);
    		},
        'flash' => function() {
          return [
            'success' => request()->get('success')
          ];
        }
        ]);
    }
}
