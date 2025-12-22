<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWalletTransactionsAddRefAndAuditCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // ref link
            $table->string('ref_type', 50)->nullable()->after('wallet_id');
            $table->unsignedBigInteger('ref_id')->nullable()->after('ref_type');

            // idempotency + meta
            $table->string('idempotency_key', 120)->nullable()->after('description');
            $table->json('meta')->nullable()->after('idempotency_key');

            // audit snapshot
            $table->decimal('balance_before', 15, 2)->nullable()->after('amount');
            $table->decimal('balance_after', 15, 2)->nullable()->after('balance_before');
            $table->decimal('held_before', 15, 2)->nullable()->after('balance_after');
            $table->decimal('held_after', 15, 2)->nullable()->after('held_before');

            // indexes
            $table->index(['ref_type', 'ref_id'], 'wtx_ref_type_ref_id_index');
            $table->unique(['wallet_id', 'idempotency_key'], 'wtx_wallet_id_idempotency_key_unique');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // drop indexes first
            $table->dropUnique('wtx_wallet_id_idempotency_key_unique');
            $table->dropIndex('wtx_ref_type_ref_id_index');

            // drop columns
            $table->dropColumn([
                'ref_type',
                'ref_id',
                'idempotency_key',
                'meta',
                'balance_before',
                'balance_after',
                'held_before',
                'held_after',
            ]);
        });
    }
}
