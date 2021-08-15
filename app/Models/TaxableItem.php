<?php

namespace App\Models;

/**
 * Eloquent class to describe the TaxableItem table.
 *
 * automatically generated by ModelGenerator.php
 */
class TaxableItem extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'TaxableItem';

    public $primaryKey = 'Id';

    protected $fillable = ['taxType', 'amount', 'subType', 'rate'];

    public function invoiceLine()
    {
        return $this->belongsTo('App\Models\InvoiceLine', 'invoiceline_id', 'Id');
    }
}