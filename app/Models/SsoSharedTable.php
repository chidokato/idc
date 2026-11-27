<?php

namespace App\Models;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SsoSharedTable extends Model
{
    protected $table = 'sso_shared_tables';

    protected $fillable = [
        'system_key',
        'system_name',
        'connection_name',
        'database_name',
        'table_name',
        'role_type',
        'employee_code_col',
        'email_col',
        'secondary_email_col',
        'phone_col',
        'secondary_phone_col',
        'username_col',
        'password_col',
        'name_col',
        'can_login',
        'can_sync',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'can_login' => 'boolean',
        'can_sync' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getDbConnection(): ConnectionInterface
    {
        $connKey = 'sso_dyn_' . $this->id;
        if (!Config::has("database.connections.{$connKey}")) {
            $base = Config::get('database.connections.mysql');
            $base['database'] = $this->database_name;
            $base['strict'] = false;
            Config::set("database.connections.{$connKey}", $base);
        }
        return DB::connection($connKey);
    }

    public function checkHealth(): array
    {
        try {
            $conn = $this->getDbConnection();
            $schema = $conn->getSchemaBuilder();
            if (!$schema->hasTable($this->table_name)) {
                return ['ok' => false, 'message' => "Không thấy bảng {$this->database_name}.{$this->table_name}"];
            }
            return ['ok' => true, 'message' => 'Kết nối OK'];
        } catch (Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public static function getActiveTables()
    {
        try {
            if (Schema::hasTable('sso_shared_tables')) {
                return self::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
            }
        } catch (Throwable $e) {
        }
        return collect();
    }
}
