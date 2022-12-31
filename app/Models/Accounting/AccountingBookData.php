<?php

namespace App\Models\Accouting
;

use AccountingBookDataDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\AccountingBook;
use App\Models\AccountingBookDataDetails;

class AccountingBookData extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'reference_code',
        'accounting_book_id',
        'transaction_date',
        'debit',
        'credit',
        'approved_by',
        'accepted_by',
        'rejected_by',
        'rejection_reason',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'transaction_date' => 'datetime',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function AccountingBook()
    {
        return $this->belongsTo(AccountingBook::class);
    }

    public function Entries()
    {
        return $this->hasMany(AccountingBookDataDetails::class);
    }
    
}
