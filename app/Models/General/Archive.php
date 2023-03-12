<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    protected $table = 'archives';

    public $primaryKey = 'id';

    protected $fillable = ['start_date', 'end_date', 'receiver_id', 'issuer_id', 'status'];

    public function issuer()
    {
        return $this->belongsTo('App\Models\ETA\Issuer', 'issuer_id', 'Id');
    }

    public function receiver()
    {
        return $this->belongsTo('App\Models\ETA\Receiver', 'receiver_id', 'Id');
    }
}
