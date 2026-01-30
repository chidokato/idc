<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskCostPeriodTable extends Migration
{
    public function up()
    {
        Schema::create('task_cost_period', function (Blueprint $table) {
            $table->increments('id');

            // Tháng + kỳ trong tháng
            $table->char('year_month', 7)->index();           // '2026-01'
            $table->unsignedTinyInteger('period_no')->index(); // 1 hoặc 2
            $table->date('period_start')->index();            // 01-15
            $table->date('period_end')->index();              // 16-end

            // Nếu hệ thống bạn đã có report_id theo kỳ
            $table->string('report_id')->nullable()->index();

            // Dimension
            $table->string('department_id', 100)->nullable()->index();
            $table->string('department_lv1', 20)->nullable()->index();
            $table->string('department_lv2', 20)->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('channel_id', 100)->nullable()->index();

            // Tổng chi phí
            $table->decimal('sum_actual', 18, 2)->default(0);

            $table->timestamp('last_calc_at')->nullable();
            $table->timestamps();

            // Unique theo đúng "1 kỳ + 1 combo dimension"
            $table->unique([
                'year_month',
                'period_no',
                'report_id',
                'department_id',
                'department_lv1',
                'department_lv2',
                'user_id',
                'channel_id',
            ], 'uq_task_cost_period_dim');
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_cost_period');
    }
}
