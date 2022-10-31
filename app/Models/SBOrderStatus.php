<?php

namespace App\Models;

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
