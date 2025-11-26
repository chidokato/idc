<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Models\Setting;
use App\Models\Department;
use App\Models\Menu;
use App\Models\User;
use App\Helpers\TreeHelper;

class AccountController extends HomeController
{
    public function dangnhap()
    {
        return view('account.login');
    }

    public function index()
    {
        $user = User::find(Auth::User()->id);
        return view('account.main', compact(
            'user',
        ));

    }

    public function edit()
    {
        $user = User::find(Auth::id());
        $departments = Department::with('children')->get();
        // Convert thành dạng tree option
        $departmentOptions = \App\Helpers\TreeHelper_disabled::buildDepartmentOptions(
            $departments,
            parent: 0,
            prefix: '',
            selectedId: $user->department_id
        );
        return view('account.edit', compact('user', 'departmentOptions'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'   => 'required|max:255',
            'phone'  => 'nullable',
            'address' => 'nullable',
        ]);

        $user = User::find(Auth::id());

        $user->update($request->only(['name', 'phone', 'address', 'department_id']));

        return redirect()->back()->with('success', 'Thành công!');
    }


    public function mktregister()
    {
        if (Auth::User()->department_id == null) {
            return redirect()->route('account.edit')->with('center_error','Cần cập nhật [ Sàn / Nhóm ] trước khi đăng ký MKT');
        }else{
            return view('account.mktregister');
        }
        
    }
}
