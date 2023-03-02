<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SBItemTax;

class MSItemMap extends Model
{
    use HasFactory;

    protected $table = 'ms_items_map';
    protected $primaryKey = 'MSCode';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'MSCode',
        'ItemNameA',
        'ItemNameE',
        'ETACode',
    ];

    protected $casts = [
        
    ];
}
