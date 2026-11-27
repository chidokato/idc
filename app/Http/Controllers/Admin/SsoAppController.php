<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SsoApp;
use Illuminate\Http\Request;

class SsoAppController extends Controller
{
    public function index()
    {
        $apps = SsoApp::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.sso_apps.index', compact('apps'));
    }

    public function updateAll(Request $request)
    {
        $items = $request->input('apps', []);

        foreach ($items as $id => $data) {
            $app = SsoApp::find($id);
            if (!$app) {
                continue;
            }
            $app->update([
                'name' => trim((string) ($data['name'] ?? $app->name)),
                'short' => trim((string) ($data['short'] ?? $app->short)),
                'url' => rtrim(trim((string) ($data['url'] ?? $app->url)), '/'),
                'callback_path' => '/' . ltrim(trim((string) ($data['callback_path'] ?? '/sso/callback')), '/'),
                'description' => trim((string) ($data['description'] ?? '')),
                'icon' => trim((string) ($data['icon'] ?? $app->icon)),
                'color' => trim((string) ($data['color'] ?? $app->color)),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'is_active' => !empty($data['is_active']),
            ]);
        }

        return redirect()->route('admin.sso-apps.index')
            ->with('Success', 'Đã lưu cấu hình danh sách phần mềm SSO thành công!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_key' => 'required|alpha_dash|max:50|unique:sso_apps,app_key',
            'name' => 'required|string|max:255',
            'short' => 'required|string|max:100',
            'url' => 'required|url|max:500',
            'callback_path' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:30',
        ]);

        SsoApp::create([
            'app_key' => strtolower($validated['app_key']),
            'name' => $validated['name'],
            'short' => $validated['short'],
            'url' => rtrim($validated['url'], '/'),
            'callback_path' => '/' . ltrim($validated['callback_path'] ?: 'sso/callback', '/'),
            'description' => $validated['description'] ?? '',
            'icon' => $validated['icon'] ?: 'fa-solid fa-cube',
            'color' => $validated['color'] ?: '#1a2c5e',
            'sort_order' => (int) (SsoApp::max('sort_order') + 1),
            'is_active' => true,
        ]);

        return redirect()->route('admin.sso-apps.index')
            ->with('Success', 'Đã thêm phần mềm mới vào cổng SSO!');
    }

    public function destroy($id)
    {
        $app = SsoApp::findOrFail($id);
        $app->delete();

        return redirect()->route('admin.sso-apps.index')
            ->with('Success', 'Đã xóa phần mềm khỏi cổng SSO.');
    }
}
