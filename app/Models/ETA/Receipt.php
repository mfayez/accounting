<?php

namespace App\Models\ETA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReceiptTaxTotal;
use Illuminate\Support\Carbon;

class Receipt extends Model
{
    protected $table = 'receipts';

    public $primaryKey = 'id';

    public $timestamps = false;
    protected $casts = [
        'id'                        => 'integer',
        'adjustment'                => 'float', 
        'exchangeRate'              => 'float', 
        'feesAmount'                => 'float', 
        'grossWeight'               => 'float', 
        'netAmount'                 => 'float', 
        'netWeight'                 => 'float', 
        'totalAmount'               => 'float', 
        'totalCommercialDiscount'   => 'float', 
        'totalItemsDiscount'        => 'float', 
        'totalSales'                => 'float'
    ];

    protected $fillable = ['buyer_id', 'buyer_mobileNumber', 'buyer_name', 'buyer_paymentNumber', 
        'buyer_type', 'currency', 'dateTimeIssued', 'pos_id', 'orderdeliveryMode', 'paymentMethod', 
        'previousUUID', 'receiptNumber', 'referenceOldUUID', 'sOrderNameCode', 'uuid', 'adjustment', 
        'exchangeRate', 'feesAmount', 'grossWeight', 'netAmount', 'netWeight', 'totalAmount', 'totalCommercialDiscount', 
        'totalItemsDiscount', 'totalSales', 'status', 'statusReason', 'submission_id', 'long_id'];

    public function getDates()
    {
        return ['dateTimeIssued'];
    }

    protected function serializeDate($date)
    {
        return $date->format('Y-m-d\TH:i:s\Z');
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
