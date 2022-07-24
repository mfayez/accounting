<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait RequestManipulators {
    
    private function convertKeysToCamelCase($data)
	{    
        $keys = array_map(function ($i) use (&$data) {
            if (is_array($data[$i]))
                $data[$i] = $this->convertKeysToCamelCase($data[$i]);
    
            $parts = explode('_', $i);
            return array_shift($parts) . implode('', array_map('ucfirst', $parts));
        }, array_keys($data));
    
        return array_combine($keys, $data);
    }
}