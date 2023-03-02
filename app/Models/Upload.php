<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
    protected $table = 'Uploads';

    public $primaryKey = 'Id';

	protected $casts = [
		'userId' => 'integer',
		'recordsCount' => 'integer',
    ];

    protected $fillable = ['userId', 'recordsCount', 'fileName', 'status'];

	public function invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'upload_id', 'Id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'userId', 'id');
    }
}
