<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskCostMonthlyTable extends Migration
{
    public function up()
    {
        Schema::create('task_cost_monthly', function (Blueprint $table) {
            $table->increments('id');

            $table->char('year_month', 7)->index();
            $table->string('report_id')->nullable()->index();

            $table->string('department_id', 100)->nullable()->index();
            $table->string('department_lv1', 20)->nullable()->index();
            $table->string('department_lv2', 20)->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('channel_id', 100)->nullable()->index();

            $table->decimal('sum_actual', 18, 2)->default(0);

            $table->timestamp('last_calc_at')->nullable();
            $table->timestamps();

            $table->unique([
                'year_month',
                'report_id',
                'department_id',
                'department_lv1',
                'department_lv2',
                'user_id',
                'channel_id',
            ], 'uq_task_cost_monthly_dim');
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_cost_monthly');
    }
}
