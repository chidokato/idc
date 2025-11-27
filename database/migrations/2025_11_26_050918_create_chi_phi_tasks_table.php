<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChiPhiTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Người thêm tác vụ');
            $table->string('user', 100)->comment('Người dùng tác vụ');
            $table->string('du_an', 255)->comment('Dự án');
            $table->decimal('chi_phi_du_kien', 15, 2)->default(0)->comment('Chi phí dự kiến');
            $table->decimal('chi_phi_thuc_te', 15, 2)->default(0)->comment('Chi phí thực tế');
            $table->decimal('ti_le_ho_tro', 5, 2)->default(0)->comment('Tỉ lệ hỗ trợ (%)');
            $table->boolean('xac_nhan')->default(false)->comment('0: Chưa xác nhận, 1: Đã xác nhận');
            $table->string('kenh', 100)->nullable()->comment('Kênh');
            $table->string('san', 100)->nullable()->comment('Sàn');
            $table->dateTime('thoi_gian')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Thời gian');
            $table->string('loai_chi_phi', 100)->nullable()->comment('Loại chi phí');
            $table->text('ghi_chu')->nullable()->comment('Ghi chú');
            $table->timestamps();

            // Khóa ngoại nếu muốn liên kết với bảng users:
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
}
