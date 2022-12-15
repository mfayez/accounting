<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SBBranchMap extends Model
{
    use HasFactory;

    protected $table = 'sb_branches_map';
    protected $primaryKey = 'branch_id';
    public $incrementing = false;
    
    protected $fillable = [
        'branch_id',
        'sb_url',
        'sb_bu',
    ];
}
