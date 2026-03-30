<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('withdrawal_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('withdrawal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->enum('action', ['approve', 'reject']);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('withdrawal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_histories');
    }
};
