<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SsoSharedTable;
use Illuminate\Http\Request;

class SsoSharedTableController extends Controller
{
    public function index()
    {
        $tables = SsoSharedTable::orderBy('sort_order')->orderBy('id')->get();
        $health = [];
        foreach ($tables as $tbl) {
            $health[$tbl->id] = $tbl->checkHealth();
        }
        return view('admin.sso_shared_tables.index', compact('tables', 'health'));
    }

    public function updateAll(Request $request)
    {
        $items = $request->input('tables', []);

        foreach ($items as $id => $data) {
            $tbl = SsoSharedTable::find($id);
            if (!$tbl) {
                continue;
            }
            $tbl->update([
                'system_name' => trim((string) ($data['system_name'] ?? $tbl->system_name)),
                'database_name' => trim((string) ($data['database_name'] ?? $tbl->database_name)),
                'table_name' => trim((string) ($data['table_name'] ?? $tbl->table_name)),
                'role_type' => trim((string) ($data['role_type'] ?? $tbl->role_type)),
                'employee_code_col' => self::nullIfEmpty($data['employee_code_col'] ?? null),
                'email_col' => self::nullIfEmpty($data['email_col'] ?? null),
                'secondary_email_col' => self::nullIfEmpty($data['secondary_email_col'] ?? null),
                'phone_col' => self::nullIfEmpty($data['phone_col'] ?? null),
                'secondary_phone_col' => self::nullIfEmpty($data['secondary_phone_col'] ?? null),
                'username_col' => self::nullIfEmpty($data['username_col'] ?? null),
                'password_col' => self::nullIfEmpty($data['password_col'] ?? null),
                'name_col' => self::nullIfEmpty($data['name_col'] ?? null),
                'can_login' => !empty($data['can_login']),
                'can_sync' => !empty($data['can_sync']),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'is_active' => !empty($data['is_active']),
            ]);
        }

        return redirect()->route('admin.sso-shared-tables.index')
            ->with('Success', 'Đã lưu cấu hình các bảng dùng chung SSO!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'system_key' => 'required|alpha_dash|max:60|unique:sso_shared_tables,system_key',
            'system_name' => 'required|string|max:255',
            'database_name' => 'required|string|max:100',
            'table_name' => 'required|string|max:100',
            'role_type' => 'required|in:source_employee,source_user,sync_target',
        ]);

        SsoSharedTable::create([
            'system_key' => strtolower($validated['system_key']),
            'system_name' => $validated['system_name'],
            'connection_name' => 'mysql',
            'database_name' => trim($validated['database_name']),
            'table_name' => trim($validated['table_name']),
            'role_type' => $validated['role_type'],
            'employee_code_col' => self::nullIfEmpty($request->input('employee_code_col')),
            'email_col' => self::nullIfEmpty($request->input('email_col', 'email')),
            'secondary_email_col' => self::nullIfEmpty($request->input('secondary_email_col')),
            'phone_col' => self::nullIfEmpty($request->input('phone_col', 'phone')),
            'secondary_phone_col' => self::nullIfEmpty($request->input('secondary_phone_col')),
            'username_col' => self::nullIfEmpty($request->input('username_col')),
            'password_col' => self::nullIfEmpty($request->input('password_col', 'password')),
            'name_col' => self::nullIfEmpty($request->input('name_col', 'name')),
            'can_login' => $request->boolean('can_login', true),
            'can_sync' => $request->boolean('can_sync', true),
            'sort_order' => (int) (SsoSharedTable::max('sort_order') + 1),
            'is_active' => true,
        ]);

        return redirect()->route('admin.sso-shared-tables.index')
            ->with('Success', 'Đã thêm bảng dùng chung mới!');
    }

    public function destroy($id)
    {
        $tbl = SsoSharedTable::findOrFail($id);
        $tbl->delete();

        return redirect()->route('admin.sso-shared-tables.index')
            ->with('Success', 'Đã xóa cấu hình bảng dùng chung.');
    }

    private static function nullIfEmpty($val): ?string
    {
        $v = trim((string) $val);
        return $v === '' ? null : $v;
    }
}
