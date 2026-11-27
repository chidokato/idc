<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSsoAppsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sso_apps')) {
            Schema::create('sso_apps', function (Blueprint $table) {
                $table->id();
                $table->string('app_key', 50)->unique();
                $table->string('name', 255);
                $table->string('short', 100);
                $table->string('description', 500)->nullable();
                $table->string('url', 500);
                $table->string('callback_path', 255)->default('/sso/callback');
                $table->string('icon', 100)->default('fa-solid fa-cube');
                $table->string('color', 30)->default('#1a2c5e');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            $order = 1;
            foreach (config('sso.apps', []) as $key => $app) {
                DB::table('sso_apps')->insert([
                    'app_key' => $app['key'] ?? $key,
                    'name' => $app['name'] ?? $key,
                    'short' => $app['short'] ?? $key,
                    'description' => $app['description'] ?? '',
                    'url' => $app['url'] ?? '',
                    'callback_path' => $app['callback_path'] ?? '/sso/callback',
                    'icon' => $app['icon'] ?? 'fa-solid fa-cube',
                    'color' => $app['color'] ?? '#1a2c5e',
                    'sort_order' => $order++,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('sso_apps');
    }
}
