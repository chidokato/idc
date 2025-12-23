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
    public function index()
{
    $user = User::findOrFail(Auth::id());

    $projects = Task::query()
        ->leftJoin('posts', 'posts.id', '=', 'tasks.post_id') // lấy tên dự án
        ->selectRaw("
            tasks.post_id,
            COALESCE(posts.name, tasks.post_id) AS project_name,

            SUM(
                CAST(
                    REPLACE(REPLACE(COALESCE(tasks.expected_costs,'0'), '.', ''), ',', '')
                AS DECIMAL(15,2))
            ) AS total_expected,

            SUM(
                CAST(
                    REPLACE(REPLACE(COALESCE(tasks.actual_costs,'0'), '.', ''), ',', '')
                AS DECIMAL(15,2))
            ) AS total_actual
        ")
        ->whereNotNull('tasks.post_id')
        ->where('tasks.post_id', '!=', '')
        ->groupBy('tasks.post_id', 'project_name')
        ->orderByDesc('total_expected')
        ->limit(15)
        ->get();

    $chartLabels  = $projects->pluck('project_name')->values()->all();
    $dataExpected = $projects->pluck('total_expected')->map(fn($v) => (float)$v)->values()->all();
    $dataActual   = $projects->pluck('total_actual')->map(fn($v) => (float)$v)->values()->all();

    return view('account.main', compact(
        'user',
        'projects',
        'chartLabels',
        'dataExpected',
        'dataActual'
    ));
}

    public function dangnhap()
    {
        return view('account.login');
    }


    public function edit()
    {
        $user = User::find(Auth::id());
        $departments = Department::with('children')->get();
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
        $data = $request->all();

        $request->validate([
            'yourname'   => 'required',
            'phone'      => 'nullable',
            'address'    => 'nullable',
        ]);

        $user = User::find(Auth::id());
        if (isset($data['department_id'])) {
            // Lấy department theo form gửi lên (KHÔNG phải theo user cũ)
            $deptLv3 = Department::find($data['department_id']);
            $deptLv2 = $deptLv3?->parentDepartment;
            $deptLv1 = $deptLv2?->parentDepartment;

            // Cập nhật user
            $user->update([
                'yourname'        => $data['yourname'],
                'phone'           => $data['phone'],
                'address'         => $data['address'],
                'department_id'   => $data['department_id'],      // LV3
                'employee_code'   => $data['employee_code'] ?? null,
                'department_lv1'  => $deptLv1?->id,               // ID
                'department_lv2'  => $deptLv2?->id,               // ID
            ]);
        }else{
            // Cập nhật user
            $user->update([
                'yourname'        => $data['yourname'],
                'phone'           => $data['phone'],
                'address'         => $data['address'],
                'employee_code'   => $data['employee_code'] ?? null,
            ]);
        }
        

        return redirect()->back()->with('success', 'Thành công!');
    }



    public function mktregister()
    {
        if (Auth::User()->department_id == null) {
            return redirect()->route('account.edit')->with('center_warning','Cần cập nhật thông tin cá nhân trước khi đăng ký marketing');
        }else{
            $groupIds = Department::where('parent', Auth::user()->Department->parent)->pluck('id')->toArray();
            // dd($groupIds);
            $r = Report::where('active', 1)->count();
            if ($r > 0) {
                $users = User::where('department_lv2', Auth::User()->department_lv2)->whereNotNull('department_lv2')->where('department_lv2', '!=', '')->get();
                $posts = Post::where('sort_by', 'Product')->where('rate', '!=', null)->orderBy('name', 'asc')->get();
                $channels = Channel::where('parent', '!=', 0)->get();
                $reports = Report::where('active', 1)->orderBy('id', 'desc')->get();
                return view('account.mktregister', compact(
                    'users',
                    'channels',
                    'posts',
                    'reports',
                    'groupIds',
                ));
            }else{
                return redirect()->route('account.edit')->with('center_warning','Các kỳ đăng ký Marketing đã đóng hoặc chưa mở kỳ mới, Vui lòng thử lại sau');
            }
        }
        
    }

    public function storeTask(Request $request)
    {
        $data = $request->all();

        // Lặp qua từng dòng trong form
        foreach ($data['post_id'] as $key => $postId) {
            
            $deptLv3 = Department::find($data['department_id'][$key]);
            if (!$deptLv3) continue;

            $deptLv2 = $deptLv3->parentDepartment;   // OK
            $deptLv1 = $deptLv2?->parentDepartment;  // OK

            Task::create([
                'user_id' => Auth::id(),
                'user' => $data['user_id'][$key] ?? null,
                'post_id' => $postId,
                'channel_id' => $data['channel_id'][$key] ?? null,
                'rate' => $data['rate'][$key] ?? null,
                'days' => $data['days'][$key] ?? null,
                'approved' => 0,
                
                'department_id' => $deptLv3->id,              // lv3
                'department_lv2' => $deptLv2?->id,            // lv2
                'department_lv1' => $deptLv1?->id,            // lv1

                'content' => $data['content'][$key] ?? null,
                'expected_costs' => isset($data['expected_costs'][$key]) 
                    ? str_replace(['.', ' đ'], '', $data['expected_costs'][$key]) 
                    : 0,
                'report_id' => $data['report_id'][$key] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Đã lưu tác vụ thành công!');
    }

    public function mktlist()
    {
        if (Auth::User()->department_id == null) {
            return redirect()->route('account.edit')->with('center_warning','Cần cập nhật thông tin cá nhân trước khi đăng ký marketing');
        }else{
            $posts = Post::where('sort_by', 'Product')->orderBy('name', 'asc')->get();
            $channels = Channel::all();
            return view('account.mktlist', compact(
                'channels',
                'posts'
            ));
        }
        
    }

    public function stats()
    {
        $tasks = auth()->user()->tasks()->with(['Post'])->get();
        $total_expected = 0;
        $total_pay = 0;

        foreach ($tasks as $val) {
            $expected = $val->report->days * $val->expected_costs;
            $pay = $val->report->days * $val->expected_costs * (1 - ($val->rate ?? 0) / 100);

            $total_expected += $expected;
            $total_pay += $pay;
        }
        return view('account.partials.stats', compact('tasks', 'total_expected', 'total_pay'));
    }


    public function delete($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['status' => false, 'message' => 'Task không tồn tại']);
        }

        $task->delete();

        $user = Auth::user();

        // Lấy report đang xem
        $report = Report::find($task->report_id); // hoặc theo cách bạn đang lấy

        // $tasks = Task::where('department_id', $user->department_id)->where('report_id', $report->id)->get();
        $tasks = $report->Task()->where('user', Auth::id())->get();

        $total_expected = 0;
        $total_pay = 0;

        foreach ($tasks as $val) {
            $expected = $report->days * $val->expected_costs;
            $pay = $report->days * $val->expected_costs * (1 - ($val->rate ?? 0) / 100);

            $total_expected += $expected;
            $total_pay += $pay;
        }

        $total_project = $tasks->pluck('post_id')->unique()->count();

        return response()->json([
            'status' => true,
            'message' => 'Xóa thành công',
            'stats' => [
                'total_project' => $total_project,
                'total_expected' => number_format($total_expected, 0, ',', '.'),
                'total_pay' => number_format($total_pay, 0, ',', '.'),
            ]
        ]);
    }


}
