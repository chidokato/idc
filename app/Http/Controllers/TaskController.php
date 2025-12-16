<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Department;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TreeHelper;

class TaskController extends HomeController
{
    public function index()
    {
        $user = Auth::user();
        $reports = Report::orderBy('id','desc')->get();

        $user_department = User::where('department_lv2', $user->department_lv2)
            ->with([
                'tasks' => function ($q) {
                    $q->where('approved', 1)
                      ->with(['department', 'Post', 'Channel', 'Report']);
                }
            ])
            ->get();


        $departments = Department::orderBy('name')->get();
        $departmentOptions = TreeHelper::buildOptions(
            $departments,
            0,
            '',
            $user->department_lv2
        );
        
        return view('account.tasks', compact(
            'user_department',
            'user',
            'reports',
            'departmentOptions',
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

    public function updateRate(Request $request)
    {
        $request->validate([
            'id'   => 'required|exists:tasks,id',
            'rate' => 'required|numeric|min:0|max:100'
        ]);

        $task = Task::find($request->id);
        $task->rate = $request->rate;
        $task->save();

        return response()->json([
            'success' => true,
            'rate' => $task->rate
        ]);
    }

}
