<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSsoProfilesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sso_profiles')) {
            Schema::create('sso_profiles', function (Blueprint $table) {
                $table->id();
                $table->string('employee_code', 50)->unique();
                $table->unsignedBigInteger('hcc_employee_id')->nullable()->index();
                $table->unsignedBigInteger('hcc_user_id')->nullable()->index();
                $table->string('name', 255);
                $table->string('primary_email', 255)->nullable()->index();
                $table->json('extra_emails')->nullable();
                $table->string('primary_phone', 30)->nullable()->index();
                $table->json('extra_phones')->nullable();
                $table->timestamp('standardized_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sso_profiles');
    }
}
