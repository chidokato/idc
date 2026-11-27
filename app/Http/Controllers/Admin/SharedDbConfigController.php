<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SharedDbConfig;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SharedDbConfigController extends Controller
{
    public function __construct()
    {
        $this->ensureTableExists();
    }

    protected function ensureTableExists(): void
    {
        try {
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
            }
        } catch (\Throwable $e) {
        }
    }

    public function index(): View
    {
        $configs = SharedDbConfig::query()
            ->latest('id')
            ->paginate(15);

        return view('admin.shared_db_configs.index', compact('configs'));
    }

    public function create(): View
    {
        $config = new SharedDbConfig([
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'is_active' => true,
        ]);

        return view('admin.shared_db_configs.create', compact('config'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:100', 'unique:shared_db_configs,code'],
            'driver' => ['required', 'string', 'in:mysql,pgsql,sqlsrv,sqlite'],
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'database' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:500'],
            'table_name' => ['required', 'string', 'max:500'],
            'prefix' => ['nullable', 'string', 'max:100'],
            'charset' => ['nullable', 'string', 'max:50'],
            'collation' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $codeInput = trim($validated['code'] ?? '');
        $code = $codeInput !== '' ? Str::slug($codeInput, '_') : Str::slug($validated['name'], '_');
        $code = $this->generateUniqueCode($code);

        $tableNames = SharedDbConfig::parseTableNames($validated['table_name']);
        $normalizedTableNames = implode(', ', $tableNames);

        SharedDbConfig::create([
            'name' => $validated['name'],
            'code' => $code,
            'driver' => $validated['driver'],
            'host' => trim($validated['host']),
            'port' => (int) $validated['port'],
            'database' => trim($validated['database']),
            'username' => trim($validated['username']),
            'password' => $validated['password'] ?? '',
            'table_name' => $normalizedTableNames,
            'prefix' => trim($validated['prefix'] ?? ''),
            'charset' => trim($validated['charset'] ?? '') ?: 'utf8mb4',
            'collation' => trim($validated['collation'] ?? '') ?: 'utf8mb4_unicode_ci',
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.shared-db-configs.index')
            ->with('Success', 'Thêm cấu hình DB dùng chung thành công.');
    }

    public function edit(SharedDbConfig $sharedDbConfig): View
    {
        return view('admin.shared_db_configs.edit', [
            'config' => $sharedDbConfig,
        ]);
    }

    public function update(Request $request, SharedDbConfig $sharedDbConfig): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('shared_db_configs', 'code')->ignore($sharedDbConfig->id),
            ],
            'driver' => ['required', 'string', 'in:mysql,pgsql,sqlsrv,sqlite'],
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'database' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:500'],
            'table_name' => ['required', 'string', 'max:500'],
            'prefix' => ['nullable', 'string', 'max:100'],
            'charset' => ['nullable', 'string', 'max:50'],
            'collation' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $codeInput = trim($validated['code'] ?? '');
        $code = $codeInput !== '' ? Str::slug($codeInput, '_') : Str::slug($validated['name'], '_');
        $code = $this->generateUniqueCode($code, $sharedDbConfig->id);

        $password = $request->has('password') ? ($validated['password'] ?? '') : $sharedDbConfig->password;

        $tableNames = SharedDbConfig::parseTableNames($validated['table_name']);
        $normalizedTableNames = implode(', ', $tableNames);

        $sharedDbConfig->update([
            'name' => $validated['name'],
            'code' => $code,
            'driver' => $validated['driver'],
            'host' => trim($validated['host']),
            'port' => (int) $validated['port'],
            'database' => trim($validated['database']),
            'username' => trim($validated['username']),
            'password' => $password,
            'table_name' => $normalizedTableNames,
            'prefix' => trim($validated['prefix'] ?? ''),
            'charset' => trim($validated['charset'] ?? '') ?: 'utf8mb4',
            'collation' => trim($validated['collation'] ?? '') ?: 'utf8mb4_unicode_ci',
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.shared-db-configs.index')
            ->with('Success', 'Cập nhật cấu hình DB dùng chung thành công.');
    }

    public function destroy(SharedDbConfig $sharedDbConfig): RedirectResponse
    {
        $sharedDbConfig->delete();

        return redirect()
            ->route('admin.shared-db-configs.index')
            ->with('Success', 'Đã xóa cấu hình DB dùng chung.');
    }

    public function toggleStatus(SharedDbConfig $sharedDbConfig): JsonResponse
    {
        $sharedDbConfig->update([
            'is_active' => !$sharedDbConfig->is_active,
        ]);

        return response()->json([
            'is_active' => $sharedDbConfig->is_active,
            'label' => $sharedDbConfig->is_active ? 'Đang bật' : 'Tạm tắt',
            'message' => 'Cập nhật trạng thái thành công.',
        ]);
    }

    public function testConnection(Request $request): JsonResponse
    {
        $configId = $request->input('config_id');

        if ($configId) {
            $record = SharedDbConfig::find($configId);
            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cấu hình DB.',
                ], 404);
            }
            $connConfig = $record->toConnectionConfig();
            $rawTableName = $record->table_name;
        } else {
            $validated = $request->validate([
                'driver' => ['nullable', 'string'],
                'host' => ['required', 'string'],
                'port' => ['required', 'integer'],
                'database' => ['required', 'string'],
                'username' => ['required', 'string'],
                'password' => ['nullable', 'string'],
                'table_name' => ['required', 'string'],
                'prefix' => ['nullable', 'string'],
                'charset' => ['nullable', 'string'],
                'collation' => ['nullable', 'string'],
            ]);

            $connConfig = [
                'driver' => $validated['driver'] ?? 'mysql',
                'host' => trim($validated['host']),
                'port' => (int) $validated['port'],
                'database' => trim($validated['database']),
                'username' => trim($validated['username']),
                'password' => $validated['password'] ?? '',
                'unix_socket' => '',
                'charset' => trim($validated['charset'] ?? '') ?: 'utf8mb4',
                'collation' => trim($validated['collation'] ?? '') ?: 'utf8mb4_unicode_ci',
                'prefix' => trim($validated['prefix'] ?? ''),
                'prefix_indexes' => true,
                'strict' => false,
                'engine' => null,
            ];
            $rawTableName = trim($validated['table_name']);
        }

        $tableNames = SharedDbConfig::parseTableNames($rawTableName);
        if (empty($tableNames)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập ít nhất một tên bảng dùng chung.',
            ], 422);
        }

        $tempKey = 'shared_db_test_' . Str::random(8);

        try {
            Config::set("database.connections.{$tempKey}", $connConfig);
            DB::purge($tempKey);

            $connection = DB::connection($tempKey);
            $connection->getPdo();

            $schema = Schema::connection($tempKey);

            $missingTables = [];
            $foundTables = [];

            foreach ($tableNames as $tbl) {
                if (!$schema->hasTable($tbl)) {
                    $missingTables[] = $tbl;
                } else {
                    $foundTables[] = $tbl;
                }
            }

            if (!empty($missingTables)) {
                $missingStr = implode(', ', $missingTables);
                $foundMsg = !empty($foundTables)
                    ? (' (Đã tìm thấy bảng: ' . implode(', ', $foundTables) . ')')
                    : '';

                return response()->json([
                    'success' => false,
                    'connected_db' => true,
                    'message' => "Kết nối tới Database `{$connConfig['database']}` thành công, nhưng KHÔNG tìm thấy bảng: `{$missingStr}`!{$foundMsg}",
                ], 422);
            }

            $tablesData = [];
            $totalAllRows = 0;

            foreach ($tableNames as $tbl) {
                $columns = $schema->getColumnListing($tbl);
                $totalRows = $connection->table($tbl)->count();
                $sampleRows = $connection->table($tbl)->limit(5)->get();
                $totalAllRows += $totalRows;

                $tablesData[] = [
                    'table_name' => $tbl,
                    'columns' => $columns,
                    'total_rows' => $totalRows,
                    'sample_rows' => $sampleRows,
                ];
            }

            $firstTable = $tablesData[0];
            $tableSummaryList = collect($tablesData)
                ->map(fn ($t) => "`{$t['table_name']}` ({$t['total_rows']} dòng)")
                ->implode(', ');

            return response()->json([
                'success' => true,
                'message' => "Kết nối thành công tới DB `{$connConfig['database']}` — " . count($tablesData) . " bảng: {$tableSummaryList}.",
                'database' => $connConfig['database'],
                'table_name' => $firstTable['table_name'],
                'columns' => $firstTable['columns'],
                'total_rows' => $totalAllRows,
                'sample_rows' => $firstTable['sample_rows'],
                'tables' => $tablesData,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kết nối Database: ' . $e->getMessage(),
            ], 500);
        } finally {
            DB::purge($tempKey);
        }
    }

    protected function generateUniqueCode(string $code, ?int $ignoreId = null): string
    {
        $baseCode = $code !== '' ? $code : 'shared_table';
        $finalCode = $baseCode;
        $counter = 1;

        while (
            SharedDbConfig::query()
                ->where('code', $finalCode)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $finalCode = "{$baseCode}_{$counter}";
            $counter++;
        }

        return $finalCode;
    }
}
