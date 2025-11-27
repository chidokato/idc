<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('du_an')->nullable();
            $table->string('kenh')->nullable();
            $table->decimal('chi_phi_du_kien', 15,2)->default(0);
            $table->decimal('chi_phi_thuc_te', 15,2)->default(0);
            $table->decimal('ti_le_ho_tro', 5,2)->default(0);
            $table->boolean('xac_nhan')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
}
