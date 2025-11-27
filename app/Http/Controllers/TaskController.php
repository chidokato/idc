<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends HomeController
{
    // Hiển thị danh sách tác vụ
    public function index()
    {
        $tasks = Task::orderBy('thoi_gian','desc')->get();
        return view('account.tasks', compact('tasks'));
    }

    // Form tạo mới
    public function create()
    {
        return view('tasks.create');
    }

    // Lưu tác vụ
    public function store(Request $request)
    {
        $request->validate([
            'du_an' => 'required',
            'kenh' => 'required',
        ]);

        $projects = $request->input('du_an');
        $channels = $request->input('kenh');
        $budgets = $request->input('ngan_sach');
        $notes = $request->input('ghi_chu');
        $times = $request->input('thoi_gian');

        foreach($projects as $key => $projectId){
            Task::create([
                'user_id' => auth()->id(),
                'user' => auth()->user()->name,
                'du_an' => $projectId,
                'kenh' => $channels[$key] ?? null,
                'chi_phi_du_kien' => str_replace(['.', ' đ'], '', $budgets[$key] ?? 0),
                'ghi_chu' => $notes[$key] ?? null,
                'thoi_gian' => $times[$key] ?? now(),
            ]);
        }

        return redirect()->back()->with('success','Đã lưu tác vụ thành công!');
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
    public function report()
    {
        $report = Task::select(
            'du_an',
            'kenh',
            \DB::raw('SUM(chi_phi_du_kien) as tong_chi_phi_du_kien'),
            \DB::raw('SUM(chi_phi_thuc_te) as tong_chi_phi_thuc_te'),
            \DB::raw('AVG(ti_le_ho_tro) as ti_le_ho_tro_tb'),
            \DB::raw('SUM(xac_nhan) as tong_xac_nhan')
        )
        ->groupBy('du_an','kenh')
        ->get();

        return view('tasks.report', compact('report'));
    }
}
