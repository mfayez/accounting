<?php

namespace App;

/**
 * Eloquent class to describe the Address table.
 *
 * automatically generated by ModelGenerator.php
 */
class Address extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'Address';

    public $primaryKey = 'Id';

    protected $fillable = ['branchID', 'country', 'governate', 'regionCity', 'street', 'buildingNumber', 'postalCode',
        'floor', 'room', 'landmark', 'additionalInformation'];

    public function issuer()
    {
        return $this->hasMany('App\Issuer', 'address_id', 'Id');
    }

    public function receiver()
    {
        return $this->hasMany('App\Receiver', 'address_id', 'Id');
    }
}
