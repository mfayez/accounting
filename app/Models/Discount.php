<?php

namespace App\Models;

/**
 * Eloquent class to describe the Discount table.
 *
 * automatically generated by ModelGenerator.php
 */
class Discount extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'Discount';

    public $primaryKey = 'Id';

    protected $fillable = ['rate', 'amount'];

    public function invoiceLine()
    {
        return $this->hasMany('App\Models\InvoiceLine', 'discount_id', 'Id');
    }
}