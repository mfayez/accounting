<?php

use App\Models\General\Settings;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

function translations($json)
{
    if(!file_exists($json)) {
		return [];
    }
    return json_decode(file_get_contents($json), true);
}

function SETTINGS_VAL($type, $key, $default)
{
	try
	{
		$item = Settings::where("type", "=", $type)->where("name", "=", $key)->first();
		if ($item)
			return $item->value;
		return $default;
	}
	catch(Exception $e)
	{
	}
	return $default;
}

function SETTINGS_SET_VAL($type, $key, $newValue)
{
	try
	{
		$item = Settings::where("type", "=", $type)->where("name", "=", $key)->first();
		if ($item)
		{
			$item->value = $newValue;
			$item->save();
		}
		else
		{
			$item = new Settings();
			$item->type = $type;
			$item->name = $key;
			$item->value = $newValue;
			$item->save();
		}
	}
	catch(Exception $e)
	{
	}
}

function BranchLogo($branchId){
	if(count($imageDir = Storage::allFiles('public/uploads/branchesImages/' . $branchId)) > 0) {
		return Str::of($imageDir[0])->replaceFirst('public' , '/storage');
	} else {
		return asset('images/invoice_logo.jpg');
	}
}
