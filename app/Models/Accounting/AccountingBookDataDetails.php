<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accounting\AccountingBookData;
use App\Models\Accounting\AccountingChart;

class AccountingBookDataDetails extends Model
{
    use HasFactory;
    /*
    $table->id();
            $table->foreignId('accounting_book_data_id');
            $table->foreignId('accounting_chart_id');
            $table->decimal('debit');
            $table->decimal('credit');
            $table->timestamps();
    */
    protected $fillable = [
        'accounting_book_data_id',
        'accounting_chart_id',
        'debit',
        'credit',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function accountingBookData()
    {
        return $this->belongsTo(AccountingBookData::class);
    }

    public function accountingChart()
    {
        return $this->belongsTo(AccountingChart::class);
    }
}
