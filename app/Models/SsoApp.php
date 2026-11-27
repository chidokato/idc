<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SsoApp extends Model
{
    protected $table = 'sso_apps';

    protected $fillable = [
        'app_key',
        'name',
        'short',
        'description',
        'url',
        'callback_path',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function getActiveApps(): array
    {
        try {
            if (Schema::hasTable('sso_apps')) {
                $rows = self::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
                $result = [];
                foreach ($rows as $row) {
                    $result[$row->app_key] = [
                        'key' => $row->app_key,
                        'name' => $row->name,
                        'short' => $row->short,
                        'description' => $row->description,
                        'url' => rtrim($row->url, '/'),
                        'callback_path' => $row->callback_path ?: '/sso/callback',
                        'icon' => $row->icon ?: 'fa-solid fa-cube',
                        'color' => $row->color ?: '#1a2c5e',
                    ];
                }
                return $result;
            }
        } catch (Throwable $e) {
        }

        return config('sso.apps', []);
    }

    public static function findActiveByKey(?string $key): ?array
    {
        if (!$key) {
            return null;
        }
        $key = strtolower(trim($key));
        $apps = self::getActiveApps();
        return $apps[$key] ?? null;
    }
}
