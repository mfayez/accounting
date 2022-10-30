<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SBItemMap extends Model
{
    use HasFactory;

    protected $table = 'sb_items_map';
    protected $primaryKey = 'SBCode';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'SBCode',
        'ItemNameA',
        'ItemNameE',
        'ETACode',
    ];
}
