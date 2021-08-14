<?php

namespace App\Models;

/**
 * Eloquent class to describe the InvoiceLine table.
 *
 * automatically generated by ModelGenerator.php
 */
class InvoiceLine extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'InvoiceLine';

    public $primaryKey = 'Id';

    protected $fillable = ['description', 'itemType', 'itemCode', 'unitType', 'quantity', 'internalCode', 'salesTotal',
        'total', 'valueDifference', 'totalTaxableFees', 'netTotal', 'itemsDiscount'];

    public function discount()
    {
        return $this->belongsTo('App\Models\Discount', 'discount_id', 'Id');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice', 'invoice_id', 'Id');
    }

    public function value()
    {
        return $this->belongsTo('App\Models\Value', 'unitValue_id', 'Id');
    }

    public function taxableItem()
    {
        return $this->hasMany('App\Models\TaxableItem', 'invoiceline_id', 'Id');
    }
}
