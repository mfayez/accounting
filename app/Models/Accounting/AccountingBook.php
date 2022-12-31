<?php

namespace App\Models\Accouting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function AccountingBookData()
    {
        return $this->hasMany(AccountingBookData::class);
    }

}
