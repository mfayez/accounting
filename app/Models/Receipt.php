<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReceiptTaxTotal;

class Receipt extends Model
{
    protected $table = 'receipts';

    public $primaryKey = 'id';

    public $timestamps = false;
    protected $casts = [
        'id'                        => 'integer',
        'adjustment'                => 'decimal:5', 
        'exchangeRate'              => 'decimal:5', 
        'feesAmount'                => 'decimal:5', 
        'grossWeight'               => 'decimal:5', 
        'netAmount'                 => 'decimal:5', 
        'netWeight'                 => 'decimal:5', 
        'totalAmount'               => 'decimal:5', 
        'totalCommercialDiscount'   => 'decimal:5', 
        'totalItemsDiscount'        => 'decimal:5', 
        'totalSales'                => 'decimal:5'
    ];

    protected $fillable = ['buyer_id', 'buyer_mobileNumber', 'buyer_name', 'buyer_paymentNumber', 
        'buyer_type', 'currency', 'dateTimeIssued', 'pos_id', 'orderdeliveryMode', 'paymentMethod', 
        'previousUUID', 'receiptNumber', 'referenceOldUUID', 'sOrderNameCode', 'uuid', 'adjustment', 
        'exchangeRate', 'feesAmount', 'grossWeight', 'netAmount', 'netWeight', 'totalAmount', 'totalCommercialDiscount', 
        'totalItemsDiscount', 'totalSales', 'status', 'statusReason'];

    public function getDates()
    {
        return ['dateTimeIssued'];
    }

    public function normalize()
    {
        //TODO MFayez fix this
        $netSale = 0;
        $totalSale = 0;
        $total = 0;
        foreach ($this->receiptItems as $line) {
            $netSale += $line->netSale;
            $totalSale += $line->totalSale;
            $total += $line->total;
        }
        $this->netAmount = $netSale;
        $this->totalSales = $totalSale;
        $this->totalAmount = $total;
    }

    public function updateTaxTotals()
    {
        $this->taxTotals()->delete();
        $taxTotals = array();
        foreach ($this->receiptItems as $line) {
            foreach ($line->taxableItems as $item) {
                if (isset($taxTotals[$item->taxType])) {
                    $taxTotals[$item->taxType] += $item->amount;
                } else {
                    $taxTotals[$item->taxType] = $item->amount;
                }

            }
        }
        foreach ($taxTotals as $key => $item) {
            $totalTax = new ReceiptTaxTotal();
            $totalTax->taxType = $key;
            $totalTax->amount = $item;
            $totalTax->receipt_id = $this->id;
            $totalTax->save();
        }
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\POS', 'pos_id', 'id');
    }
    public function receiptItems()
    {
        return $this->hasMany('App\Models\ReceiptItem', 'receipt_id', 'id');
    }

    public function taxTotals()
    {
        return $this->hasMany('App\Models\ReceiptTaxTotal', 'receipt_id', 'id');
    }
}
