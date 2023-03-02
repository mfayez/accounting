<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SBItemTax extends Model
{
    use HasFactory;

    protected $table = 'sb_item_tax';
    
    protected $fillable = [
        'SBCode',
        'taxType',
        'taxSubtype',
        'rate',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
    ];

    public $timestamps = false;

}
