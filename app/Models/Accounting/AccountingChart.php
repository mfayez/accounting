<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingChart extends Model
{
    use HasFactory;

    protected $table = 'accounting_chart';
    
    protected $fillable = [
        'id',
        'name',
        'parent_id',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function parent()
    {
        return $this->belongsTo(AccountingChart::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(AccountingChart::class, 'parent_id');
    }
}
