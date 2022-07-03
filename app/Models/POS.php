<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POS extends Model
{
    protected $table = 'pos';

    public $primaryKey = 'id';
 
    protected $fillable = ["name", "serial", "os_version", "model",
        "grant_type", "pos_key", "activity_code", "client_id", "client_secret", 'issuer_id'];

    public function issuer()
    {
        return $this->belongsTo('App\Models\Issuer', 'issuer_id', 'Id');
    }

}
