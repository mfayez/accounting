<?php

namespace App\Models\SalesBuzz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SBOrderStatus extends Model
{
    use HasFactory;

    protected $table = 'sb_order_status';
    protected $fillable = [
        'order_status',
        'description',
    ];
}
