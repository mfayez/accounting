<?php

use App\Models\Settings;
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
