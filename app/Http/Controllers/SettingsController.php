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
use Illuminate\Support\Facades\File; 

use App\Models\General\Settings;



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
			//if $request->input($key) is array convert to string
			if (is_array($request->input($key))){
				$item->value = json_encode($request->input($key));
			}
			else{
				$item->value = $request->input($key);
			}
			$item->save();
		}
	}

	public function indexActivityCodes_json()
	{
		$settings = Settings::where("name", "activities")->where("type", "Company Settings")->first();
		if ($settings){
			return json_decode($settings->value);
		}
		else{
			//if file exists
			if(File::exists(public_path().'/json/ActivityCodes.json')){
				//get data from file
				$settings = file_get_contents(public_path().'/json/ActivityCodes.json'); 
				return json_decode($settings);
			}
			else {
				return [];
			}
		}
	}
}
