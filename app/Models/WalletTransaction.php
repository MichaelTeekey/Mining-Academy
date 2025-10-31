<?php

namespace App\Models;

class WalletTransaction extends BaseModel
{
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'description',
        'reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
