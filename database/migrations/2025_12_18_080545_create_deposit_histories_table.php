<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('deposit_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('deposit_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('admin_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('action', ['approve', 'reject', 'rollback']);

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index('deposit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_histories');
    }
}
