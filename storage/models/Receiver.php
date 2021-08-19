<?php

namespace App;

/**
 * Eloquent class to describe the Receiver table.
 *
 * automatically generated by ModelGenerator.php
 */
class Receiver extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'Receiver';

    public $primaryKey = 'Id';

    protected $fillable = ['type', 'receiver_id', 'name'];

    public function address()
    {
        return $this->belongsTo('App\Address', 'address_id', 'Id');
    }

    public function invoice()
    {
        return $this->hasMany('App\Invoice', 'receiver_id', 'Id');
    }
}
