<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SBItemTax;

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
        'Val_Diff',
    ];

    protected $casts = [
        'Val_Diff' => 'decimal:2'
    ];

    public function itemTax()
    {
        return $this->hasMany(SBItemTax::class, 'SBCode', 'SBCode');
    }
}
