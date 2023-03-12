<?php

namespace App\Models\Mobis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MSBranchMap extends Model
{
    use HasFactory;

    protected $table = 'ms_branches_map';
    protected $primaryKey = 'branch_id';
    public $incrementing = false;
    
    protected $fillable = [
        'branch_id',
        'ms_url',
    ];
}
