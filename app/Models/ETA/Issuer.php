<?php

namespace App\Models\ETA;

/**
 * Eloquent class to describe the Issuer table.
 *
 * automatically generated by ModelGenerator.php
 */
class Issuer extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'Issuer';

    public $primaryKey = 'Id';

    protected $fillable = ['type', 'issuer_id', 'name'];

    public function address()
    {
        return $this->belongsTo('App\Models\ETA\Address', 'address_id', 'Id');
    }

    public function invoice()
    {
        return $this->hasMany('App\Models\ETA\Invoice', 'issuer_id', 'Id');
    }
}
