<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\Settings;



class SettingsController extends Controller
{
	public function index_json(Request $request)
	{
		$settings = Settings::where("type", "=", $request->input('type'))->get();
		//$settings = Settings::all();
		$keys = $settings->pluck('name')->toArray();
		$values = $settings->pluck('value')->toArray();
		return array_combine($keys, $values);
	}
	
	public function store(Request $request)
	{
		$type = $request->input('type');
		$settings = array_keys($request->except(['type', "isDirty", "errors", "hasErrors", "processing", 
				"progress", "wasSuccessful", "recentlySuccessful", "__rememberable"]));
		foreach($settings as $key){
			$item = Settings::where("type", "=", $type)->where("name", "=", $key)->first();
			if (!$item){
				$item = new Settings();
				$item->type = $type;
				$item->name = $key;
			}
			$item->value = $request->input($key);
			$item->save();
		}
	}
}
