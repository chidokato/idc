<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    public function charge(User $user, float $amount, string $description): void
    {
        DB::transaction(function () use ($user, $amount, $description) {

            $wallet = $user->wallet()->lockForUpdate()->first();

            if (!$wallet) {
                throw new Exception('User chưa có ví');
            }

            if ($wallet->balance < $amount) {
                throw new Exception('Số dư không đủ');
            }

            $wallet->withdraw($amount, $description);
        });
    }
}
