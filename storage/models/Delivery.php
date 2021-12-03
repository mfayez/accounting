<?php

namespace App;

/**
 * Eloquent class to describe the Delivery table.
 *
 * automatically generated by ModelGenerator.php
 */
class Delivery extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'Delivery';

    public $primaryKey = 'Id';

    protected $fillable = ['approach', 'packaging', 'dateValidity', 'exportPort', 'grossWeight', 'netWeight',
        'terms'];

    public function getDates()
    {
        return ['dateValidity'];
    }

    public function invoice()
    {
        return $this->hasMany('App\Invoice', 'delivery_id', 'Id');
    }
}