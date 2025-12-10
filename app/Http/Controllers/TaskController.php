<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Department;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class TaskController extends HomeController
{
    public function index()
    {
        $user = Auth::user();
        $depLv3 = $user->department;
        $depLv2 = $depLv3?->parentDepartment;
        $depLv1 = $depLv2?->parentDepartment;
        $reports = Report::orderBy('id','desc')->get();
        if (!$depLv2) {
            $tasks = Task::with(['User.department.parentDepartment', 'Post', 'Channel'])
                         ->where('user_id', $user->id)
                         ->orderBy('id','desc')
                         ->get();
        } else {
            $tasks = Task::with(['User.department.parentDepartment', 'Post', 'Channel'])
                ->whereHas('User', function($qUser) use ($depLv2) {
                    // user có department là 1 trong các group lv3 của phòng lv2
                    $qUser->whereHas('department', function($qDept) use ($depLv2) {
                        $qDept->where('parent', $depLv2->id);
                    });
                })
                ->orderBy('id','desc')
                ->get();
        }
            
        return view('account.tasks', compact(
            'tasks',
            'user',
            'depLv3',
            'depLv2',
            'depLv1',
            'reports',
        ));
    }

    public function toggleApproved(Request $request, Task $task)
    {
        // Lấy trạng thái từ request
        $approved = $request->input('approved') == 'true' ? 1 : 0;

        $task->approved = $approved;
        $task->save();

        return response()->json([
            'success' => true,
            'approved' => $task->approved,
        ]);
    }

    // Form tạo mới
    public function create()
    {
        return view('tasks.create');
    }

    // Lưu tác vụ
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'du_an' => 'required',
    //         'kenh' => 'required',
    //     ]);

    //     $projects = $request->input('du_an');
    //     $channels = $request->input('kenh');
    //     $budgets = $request->input('ngan_sach');
    //     $notes = $request->input('ghi_chu');
    //     $times = $request->input('thoi_gian');

    //     foreach($projects as $key => $projectId){
    //         Task::create([
    //             'user_id' => auth()->id(),
    //             'user' => auth()->user()->name,
    //             'du_an' => $projectId,
    //             'kenh' => $channels[$key] ?? null,
    //             'chi_phi_du_kien' => str_replace(['.', ' đ'], '', $budgets[$key] ?? 0),
    //             'ghi_chu' => $notes[$key] ?? null,
    //             'thoi_gian' => $times[$key] ?? now(),
    //         ]);
    //     }

    //     return redirect()->back()->with('success','Đã lưu tác vụ thành công!');
    // }

    // Form edit
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        return view('tasks.edit', compact('task'));
    }

    // Cập nhật tác vụ
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update($request->all());
        return redirect()->route('tasks.index')->with('success','Cập nhật thành công!');
    }

    // Xóa tác vụ
    public function destroy($id)
    {
        Task::destroy($id);
        return redirect()->back()->with('success','Đã xóa tác vụ!');
    }

    // Báo cáo tổng hợp
    // public function report()
    // {
    //     $report = Task::select(
    //         'du_an',
    //         'kenh',
    //         \DB::raw('SUM(chi_phi_du_kien) as tong_chi_phi_du_kien'),
    //         \DB::raw('SUM(chi_phi_thuc_te) as tong_chi_phi_thuc_te'),
    //         \DB::raw('AVG(ti_le_ho_tro) as ti_le_ho_tro_tb'),
    //         \DB::raw('SUM(xac_nhan) as tong_xac_nhan')
    //     )
    //     ->groupBy('du_an','kenh')
    //     ->get();

    //     return view('tasks.report', compact('report'));
    // }
}
