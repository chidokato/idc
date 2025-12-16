<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'description',
    ];

    /* ================== RELATIONSHIP ================== */

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /* ================== SCOPE (TIỆN DÙNG) ================== */

    public function scopeDeposit($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeWithdraw($query)
    {
        return $query->where('type', 'withdraw');
    }
}
