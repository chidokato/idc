<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSharedDbConfigsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('shared_db_configs')) {
            Schema::create('shared_db_configs', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('driver')->default('mysql');
                $table->string('host')->default('127.0.0.1');
                $table->unsignedInteger('port')->default(3306);
                $table->string('database');
                $table->string('username');
                $table->text('password')->nullable();
                $table->string('table_name');
                $table->string('prefix')->nullable();
                $table->string('charset')->default('utf8mb4');
                $table->string('collation')->default('utf8mb4_unicode_ci');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            $defaults = [
                [
                    'name' => 'HCC (IZI) - Nhân sự & Tài khoản chuẩn',
                    'code' => 'hcc_izi',
                    'database' => 'izi',
                    'table_name' => 'employees, users',
                    'description' => 'Nguồn dữ liệu gốc nhân sự và tài khoản đăng nhập chuẩn theo Mã NV.',
                ],
                [
                    'name' => 'Web chính & MKT (IDC)',
                    'code' => 'idc_users',
                    'database' => 'idc',
                    'table_name' => 'users',
                    'description' => 'Bảng tài khoản website chính và hệ thống Marketing.',
                ],
                [
                    'name' => 'Learning (Đào tạo)',
                    'code' => 'learning_users',
                    'database' => 'learning',
                    'table_name' => 'users',
                    'description' => 'Bảng tài khoản hệ thống đào tạo nội bộ.',
                ],
                [
                    'name' => 'Indochine CRM BĐS',
                    'code' => 'crm_users',
                    'database' => 'bds_crm',
                    'table_name' => 'users',
                    'description' => 'Bảng tài khoản hệ thống CRM quản lý Leads.',
                ],
                [
                    'name' => 'IDC Tool & Dự án',
                    'code' => 'idc_tool_users',
                    'database' => 'idc_tool',
                    'table_name' => 'users',
                    'description' => 'Bảng tài khoản hệ thống tra cứu giỏ hàng & dự án.',
                ],
            ];

            foreach ($defaults as $item) {
                DB::table('shared_db_configs')->insert([
                    'name' => $item['name'],
                    'code' => $item['code'],
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => (int) env('DB_PORT', 3306),
                    'database' => $item['database'],
                    'username' => env('DB_USERNAME', 'root'),
                    'password' => env('DB_PASSWORD', ''),
                    'table_name' => $item['table_name'],
                    'prefix' => '',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'description' => $item['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('shared_db_configs');
    }
}
