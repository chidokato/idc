<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTasksAddPricingAndWalletTx extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('price_expected', 15, 2)->default(0)->after('id');
            $table->decimal('price_final', 15, 2)->nullable()->after('price_expected');

            $table->unsignedBigInteger('hold_transaction_id')->nullable()->after('price_final');
            $table->unsignedBigInteger('capture_transaction_id')->nullable()->after('hold_transaction_id');

            $table->string('status', 50)->default('registered')->after('capture_transaction_id');

            $table->index(['status']);
            $table->index(['hold_transaction_id']);
            $table->index(['capture_transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['hold_transaction_id']);
            $table->dropIndex(['capture_transaction_id']);

            $table->dropColumn([
                'price_expected',
                'price_final',
                'hold_transaction_id',
                'capture_transaction_id',
                'status',
            ]);
        });
    }
}
