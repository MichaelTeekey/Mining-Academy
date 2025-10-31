<?php

namespace App\Models;

class Wallet extends BaseModel
{
    protected $fillable = [
        'user_id',
        'balance',
        'currency',
    ];

    protected $casts = [
        'balance' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
