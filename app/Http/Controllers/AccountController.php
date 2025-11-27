<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Helpers\TreeHelper;
use App\Models\Setting;
use App\Models\Department;
use App\Models\Menu;
use App\Models\User;
use App\Models\Post;
use App\Models\Channel;
use App\Models\Task;
use App\Models\Report;

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
            $posts = Post::where('sort_by', 'Product')->orderBy('name', 'asc')->get();
            $channels = Channel::all();
            $reports = Report::where('active', 1)->orderBy('id', 'desc')->get();
            $tasks = Task::where('user_id',Auth::User()->id)->get();
            return view('account.mktregister', compact(
                'channels',
                'posts',
                'reports',
                'tasks',
            ));
        }
        
    }

    public function mktlist()
    {
        if (Auth::User()->department_id == null) {
            return redirect()->route('account.edit')->with('center_error','Cần cập nhật [ Sàn / Nhóm ] trước khi đăng ký MKT');
        }else{
            $posts = Post::where('sort_by', 'Product')->orderBy('name', 'asc')->get();
            $channels = Channel::all();
            return view('account.mktlist', compact(
                'channels',
                'posts'
            ));
        }
        
    }

    public function storeTask(Request $request)
    {
        $data = $request->all();

        // Lặp qua từng dòng trong form
        foreach ($data['post_id'] as $key => $postId) {
            Task::create([
                'user_id' => auth()->id(),
                'post_id' => $postId,
                'channel_id' => $data['channel_id'][$key] ?? null,
                'content' => $data['content'][$key] ?? null,
                'expected_costs' => isset($data['expected_costs'][$key]) 
                    ? str_replace(['.', ' đ'], '', $data['expected_costs'][$key]) 
                    : 0,
                'report_id' => $data['report_id'][$key] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Đã lưu tác vụ thành công!');
    }
}
