<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WalletTransfersPausedTest extends TestCase
{
    public function test_transfer_form_is_blocked_without_accessing_the_database()
    {
        config(['wallet.transfers_enabled' => false]);
        $this->withoutMiddleware();
        DB::shouldReceive('connection')->never();

        $this->get('/account/wallet/transfer')->assertForbidden();
    }

    public function test_transfer_submission_is_blocked_without_accessing_the_database()
    {
        config(['wallet.transfers_enabled' => false]);
        $this->withoutMiddleware();
        DB::shouldReceive('connection')->never();

        $this->post('/account/wallet/transfer', [
            'mode' => 'same',
            'recipient_ids' => [2],
            'amount' => 1000,
            'idempotency_key' => 'paused-transfer-test',
        ])->assertForbidden();
    }
}
