<?php

namespace App\Models;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SharedDbConfig extends Model
{
    use HasFactory;

    protected $table = 'shared_db_configs';

    protected $fillable = [
        'name',
        'code',
        'driver',
        'host',
        'port',
        'database',
        'username',
        'password',
        'table_name',
        'prefix',
        'charset',
        'collation',
        'description',
        'is_active',
    ];

    protected $casts = [
        'port' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function parseTableNames(?string $raw): array
    {
        if (!$raw) {
            return [];
        }

        $parts = preg_split('/[,;\r\n]+/', $raw) ?: [];
        $cleaned = array_map('trim', $parts);

        return array_values(array_unique(array_filter($cleaned, fn ($v) => $v !== '')));
    }

    public function getTableNames(): array
    {
        return static::parseTableNames($this->table_name);
    }

    public function hasSharedTable(string $tableName): bool
    {
        $target = strtolower(trim($tableName));
        foreach ($this->getTableNames() as $tbl) {
            if (strtolower($tbl) === $target) {
                return true;
            }
        }

        return false;
    }

    public static function findActiveForTable(string $tableName): ?self
    {
        $activeConfigs = static::query()->where('is_active', true)->get();

        foreach ($activeConfigs as $cfg) {
            if ($cfg->hasSharedTable($tableName)) {
                return $cfg;
            }
        }

        return $activeConfigs->first();
    }

    public function getConnectionNameKey(): string
    {
        return 'shared_db_' . ($this->code ?: $this->id);
    }

    public function toConnectionConfig(): array
    {
        return [
            'driver' => $this->driver ?: 'mysql',
            'host' => $this->host ?: '127.0.0.1',
            'port' => $this->port ?: 3306,
            'database' => $this->database,
            'username' => $this->username,
            'password' => $this->password ?? '',
            'unix_socket' => '',
            'charset' => $this->charset ?: 'utf8mb4',
            'collation' => $this->collation ?: 'utf8mb4_unicode_ci',
            'prefix' => $this->prefix ?? '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
        ];
    }

    public function connectSharedDb(): ConnectionInterface
    {
        $connKey = $this->getConnectionNameKey();
        Config::set("database.connections.{$connKey}", $this->toConnectionConfig());
        DB::purge($connKey);

        return DB::connection($connKey);
    }

    public function querySharedTable(?string $tableName = null): Builder
    {
        $tables = $this->getTableNames();
        $targetTable = $tableName ? trim($tableName) : ($tables[0] ?? $this->table_name);

        return $this->connectSharedDb()->table($targetTable);
    }

    public static function tableByCode(string $code, ?string $tableName = null): Builder
    {
        $config = static::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->firstOrFail();

        return $config->querySharedTable($tableName);
    }
}
