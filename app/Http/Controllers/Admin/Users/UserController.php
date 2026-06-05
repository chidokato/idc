<?php

namespace App\Http\Controllers\Admin\Users;

use App\Helpers\TreeHelper;
use App\Helpers\TreeHelper_disabled;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        $departmentOptions = TreeHelper::buildOptions(
            $departments,
            0,
            '',
            $request->department_id
        );

        $admins = $this->applyUserFilters(
            User::query()->where('permission', '<', 6),
            $request
        )
            ->orderByDesc('id')
            ->get();

        return view('admin.user.index', compact('admins', 'departmentOptions'));
    }

    public function member(Request $request)
    {
        $departments = Department::orderBy('name')->get();

        $departmentOptions = TreeHelper::buildOptions(
            $departments,
            0,
            '',
            $request->department_id
        );

        $users = $this->applyUserFilters(
            User::query()->where('permission', 6),
            $request
        )
            ->orderByDesc('id')
            ->get();

        return view('admin.user.member', compact('users', 'departmentOptions'));
    }

    public function create()
    {
        $departments = Department::with('children')->get();
        $departmentOptions = TreeHelper_disabled::buildDepartmentOptions(
            $departments,
            parent: 0,
            prefix: ''
        );

        return view('admin.user.create', compact('departmentOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'employee_code' => 'required|string|max:255|unique:users,employee_code',
                'yourname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'permission' => 'required|integer|in:1,2,3,6',
                'rank' => 'nullable|integer|in:1,2,3',
                'department_id' => 'required|exists:departments,id',
                'password' => 'required|min:6',
                'passwordagain' => 'required|same:password',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'facebook' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive',
            ],
            [
                'employee_code.unique' => 'Mã nhân viên đã tồn tại',
                'email.unique' => 'Email đã tồn tại',
                'passwordagain.same' => 'Mật khẩu nhập lại không khớp',
            ]
        );

        $departmentLv3 = Department::with('parentDepartment.parentDepartment')
            ->findOrFail($validated['department_id']);

        $user = new User();
        $user->employee_code = $validated['employee_code'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->permission = $validated['permission'];
        $user->rank = $validated['rank'] ?? null;
        $user->yourname = $validated['yourname'];
        $user->address = $validated['address'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->facebook = $validated['facebook'] ?? null;
        $user->status = $validated['status'];
        $user->department_id = $departmentLv3->id;
        $user->department_lv2 = $departmentLv3->parentDepartment?->id;
        $user->department_lv1 = $departmentLv3->parentDepartment?->parentDepartment?->id;
        $user->save();

        return redirect()->route('users.index')->with('success', 'Thêm người dùng thành công');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data = User::findOrFail($id);
        $department = null;

        if (!empty($data->department_id)) {
            $department = Department::find($data->department_id);
        }

        $items = Department::all();

        $options = TreeHelper::buildOptions(
            items: $items,
            parentId: 0,
            prefix: '',
            selectedId: $data->department_id,
            idField: 'id',
            parentField: 'parent',
            nameField: 'name'
        );

        return view('admin.user.edit', compact('data', 'department', 'options'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate(
            [
                'employee_code' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users', 'employee_code')->ignore($user->id),
                ],
                'yourname' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],
                'permission' => 'required|integer|in:1,2,3,6',
                'rank' => 'nullable|integer|in:1,2,3',
                'department_id' => 'required|exists:departments,id',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'facebook' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive',
                'password' => 'nullable|min:6',
                'passwordagain' => 'nullable|same:password',
            ],
            [
                'employee_code.unique' => 'Mã nhân viên đã tồn tại',
                'email.unique' => 'Email đã tồn tại',
                'passwordagain.same' => 'Mật khẩu nhập lại không khớp',
            ]
        );

        if ($request->changepassword === 'on') {
            $request->validate([
                'password' => 'required|min:6',
                'passwordagain' => 'required|same:password',
            ]);

            $user->password = bcrypt($validated['password']);
        }

        $departmentLv3 = Department::with('parentDepartment.parentDepartment')
            ->findOrFail($validated['department_id']);

        $user->email = $validated['email'];
        $user->employee_code = $validated['employee_code'];
        $user->permission = $validated['permission'];
        $user->yourname = $validated['yourname'];
        $user->rank = $validated['rank'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->facebook = $validated['facebook'] ?? null;
        $user->status = $validated['status'];
        $user->department_id = $departmentLv3->id;
        $user->department_lv2 = $departmentLv3->parentDepartment?->id;
        $user->department_lv1 = $departmentLv3->parentDepartment?->parentDepartment?->id;
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật người dùng thành công');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ((int) Auth::id() === (int) $user->id) {
            return redirect()->back()->with('error', 'Không thể xóa tài khoản đang đăng nhập');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Xóa người dùng thành công');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    public function updateName(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:users,id',
            'yourname' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($validated['id']);
        $user->yourname = $validated['yourname'];
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật tên thành công',
        ]);
    }

    public function changeStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::find($validated['id']);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy user!',
            ], 404);
        }

        $user->status = $validated['status'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái user thành công!',
        ]);
    }

    public function updateWorkStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_working' => 'required|boolean',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->is_working = $request->is_working;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật trạng thái thành công',
        ]);
    }

    private function applyUserFilters($query, Request $request)
    {
        if ($request->filled('key')) {
            $key = trim($request->key);

            $query->where(function ($q) use ($key) {
                $q->where('name', 'like', "%{$key}%")
                    ->orWhere('yourname', 'like', "%{$key}%")
                    ->orWhere('email', 'like', "%{$key}%")
                    ->orWhere('employee_code', 'like', "%{$key}%")
                    ->orWhere('phone', 'like', "%{$key}%");
            });
        }

        if ($request->filled('department_id')) {
            $departmentId = $request->department_id;

            $query->where(function ($q) use ($departmentId) {
                $q->where('department_lv1', $departmentId)
                    ->orWhere('department_lv2', $departmentId)
                    ->orWhere('department_id', $departmentId);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query;
    }
}
