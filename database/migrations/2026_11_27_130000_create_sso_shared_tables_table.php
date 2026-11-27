<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSsoSharedTablesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sso_shared_tables')) {
            Schema::create('sso_shared_tables', function (Blueprint $table) {
                $table->id();
                $table->string('system_key', 60)->unique();
                $table->string('system_name', 255);
                $table->string('connection_name', 60)->default('mysql');
                $table->string('database_name', 100);
                $table->string('table_name', 100);
                $table->string('role_type', 40)->default('sync_target'); // source_employee | source_user | sync_target
                $table->string('employee_code_col', 60)->nullable();
                $table->string('email_col', 60)->nullable();
                $table->string('secondary_email_col', 60)->nullable();
                $table->string('phone_col', 60)->nullable();
                $table->string('secondary_phone_col', 60)->nullable();
                $table->string('username_col', 60)->nullable();
                $table->string('password_col', 60)->nullable();
                $table->string('name_col', 60)->nullable();
                $table->boolean('can_login')->default(true);
                $table->boolean('can_sync')->default(true);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            $defaults = [
                [
                    'system_key' => 'izi_employees',
                    'system_name' => 'HCC (IZI) - Bảng nhân sự chuẩn',
                    'connection_name' => 'izi',
                    'database_name' => 'izi',
                    'table_name' => 'employees',
                    'role_type' => 'source_employee',
                    'employee_code_col' => 'employee_code',
                    'email_col' => 'email',
                    'secondary_email_col' => null,
                    'phone_col' => 'phone',
                    'secondary_phone_col' => null,
                    'username_col' => null,
                    'password_col' => null,
                    'name_col' => 'name',
                    'can_login' => false,
                    'can_sync' => true,
                    'sort_order' => 1,
                ],
                [
                    'system_key' => 'izi_users',
                    'system_name' => 'HCC (IZI) - Bảng tài khoản đăng nhập',
                    'connection_name' => 'izi',
                    'database_name' => 'izi',
                    'table_name' => 'users',
                    'role_type' => 'source_user',
                    'employee_code_col' => null,
                    'email_col' => 'email',
                    'secondary_email_col' => null,
                    'phone_col' => 'phone',
                    'secondary_phone_col' => 'secondary_phone',
                    'username_col' => null,
                    'password_col' => 'password',
                    'name_col' => 'name',
                    'can_login' => true,
                    'can_sync' => true,
                    'sort_order' => 2,
                ],
                [
                    'system_key' => 'idc_users',
                    'system_name' => 'Web chính & MKT (idc.users)',
                    'connection_name' => 'mysql',
                    'database_name' => 'idc',
                    'table_name' => 'users',
                    'role_type' => 'sync_target',
                    'employee_code_col' => 'employee_code',
                    'email_col' => 'email',
                    'secondary_email_col' => 'secondary_email',
                    'phone_col' => 'phone',
                    'secondary_phone_col' => null,
                    'username_col' => null,
                    'password_col' => 'password',
                    'name_col' => 'name',
                    'can_login' => true,
                    'can_sync' => true,
                    'sort_order' => 3,
                ],
                [
                    'system_key' => 'learning_users',
                    'system_name' => 'Learning / Đào tạo (learning.users)',
                    'connection_name' => 'learning',
                    'database_name' => 'learning',
                    'table_name' => 'users',
                    'role_type' => 'sync_target',
                    'employee_code_col' => 'employee_code',
                    'email_col' => 'email',
                    'secondary_email_col' => null,
                    'phone_col' => 'phone',
                    'secondary_phone_col' => 'secondary_phone',
                    'username_col' => 'username',
                    'password_col' => 'password',
                    'name_col' => 'name',
                    'can_login' => true,
                    'can_sync' => true,
                    'sort_order' => 4,
                ],
                [
                    'system_key' => 'crm_users',
                    'system_name' => 'CRM BĐS (bds_crm.users)',
                    'connection_name' => 'bds_crm',
                    'database_name' => 'bds_crm',
                    'table_name' => 'users',
                    'role_type' => 'sync_target',
                    'employee_code_col' => 'employee_code',
                    'email_col' => 'email',
                    'secondary_email_col' => null,
                    'phone_col' => 'phone',
                    'secondary_phone_col' => null,
                    'username_col' => null,
                    'password_col' => 'password_hash',
                    'name_col' => 'full_name',
                    'can_login' => true,
                    'can_sync' => true,
                    'sort_order' => 5,
                ],
                [
                    'system_key' => 'idc_tool_users',
                    'system_name' => 'IDC Tool (idc_tool.users)',
                    'connection_name' => 'idc_tool',
                    'database_name' => 'idc_tool',
                    'table_name' => 'users',
                    'role_type' => 'sync_target',
                    'employee_code_col' => 'employee_code',
                    'email_col' => 'email',
                    'secondary_email_col' => null,
                    'phone_col' => 'phone',
                    'secondary_phone_col' => null,
                    'username_col' => null,
                    'password_col' => 'password',
                    'name_col' => 'name',
                    'can_login' => true,
                    'can_sync' => true,
                    'sort_order' => 6,
                ],
            ];

            foreach ($defaults as $row) {
                $row['is_active'] = true;
                $row['created_at'] = now();
                $row['updated_at'] = now();
                DB::table('sso_shared_tables')->insert($row);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('sso_shared_tables');
    }
}
