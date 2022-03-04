<?php

namespace App;

/**
 * Eloquent class to describe the Value table.
 *
 * automatically generated by ModelGenerator.php
 */
class Value extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'Value';

    public $primaryKey = 'Id';

    protected $fillable = ['currencySold', 'amountEGP', 'amountSold', 'currencyExchangeRate'];

    public function invoiceLine()
    {
        return $this->hasMany('App\InvoiceLine', 'unitValue_id', 'Id');
    }
}
