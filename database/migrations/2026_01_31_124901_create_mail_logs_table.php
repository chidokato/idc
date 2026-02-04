<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('batch_id', 64)->index();     // gom theo 1 lần bấm gửi
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('email')->index();

            $table->string('subject', 200)->nullable();
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued')->index();

            $table->timestamp('sent_at')->nullable();
            $table->text('error')->nullable();

            $table->timestamps();

            $table->index(['batch_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
