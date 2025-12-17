<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Department;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TreeHelperLv2Only;

class TaskController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();

    $reports = Report::orderByDesc('id')->get();
    $selectedReportId = $request->input('report_id', optional($reports->first())->id);

    // filter sàn (lv2) từ dropdown
    $selectedLv2 = $request->input('department_id', $user->department_lv2);

    $departments = Department::orderBy('name')->get();
    $departmentOptions = TreeHelperLv2Only::buildOptions($departments, $selectedLv2);

    // lấy users + tasks
    $users = User::query()
        ->where('department_lv2', $selectedLv2)
        ->orderBy('department_id', 'asc')
        ->with([
            'tasks' => function ($q) use ($selectedReportId) {
                $q->where('approved', 1)
                  ->when($selectedReportId, fn($qq) => $qq->where('report_id', $selectedReportId))
                  ->with(['department', 'Post', 'Channel', 'Report']);
            }
        ])
        ->get();

    // ===== build cây: lv2 -> room(task.department_id) -> user -> tasks =====
    $grandGross = 0;
    $grandNet   = 0;

    $lv2Tree = []; // array cho nhanh

    foreach ($users as $u) {
        foreach ($u->tasks as $t) {

            $lv2Id   = (int) $u->department_lv2;
            $roomId  = (int) ($t->department_id ?? 0); // phòng/nhóm (lv3)

            $lv2Name = $departments->firstWhere('id', $lv2Id)?->name ?? ('Sàn #' . $lv2Id);
            $roomName = $t->department?->name
                ?? $departments->firstWhere('id', $roomId)?->name
                ?? ('Phòng #' . $roomId);

            // init lv2
            if (!isset($lv2Tree[$lv2Id])) {
                $lv2Tree[$lv2Id] = [
                    'id' => $lv2Id,
                    'name' => $lv2Name,
                    'gross' => 0,
                    'net' => 0,
                    'rooms' => []
                ];
            }

            // init room
            if (!isset($lv2Tree[$lv2Id]['rooms'][$roomId])) {
                $lv2Tree[$lv2Id]['rooms'][$roomId] = [
                    'id' => $roomId,
                    'name' => $roomName,
                    'gross' => 0,
                    'net' => 0,
                    'users' => []
                ];
            }

            // init user node inside room
            if (!isset($lv2Tree[$lv2Id]['rooms'][$roomId]['users'][$u->id])) {
                $lv2Tree[$lv2Id]['rooms'][$roomId]['users'][$u->id] = [
                    'id' => $u->id,
                    'employee_code' => $u->employee_code,
                    'yourname' => $u->yourname,
                    'gross' => 0,
                    'net' => 0,
                    'tasks' => []
                ];
            }

            // push task
            $lv2Tree[$lv2Id]['rooms'][$roomId]['users'][$u->id]['tasks'][] = $t;

            // cộng tổng user
            $lv2Tree[$lv2Id]['rooms'][$roomId]['users'][$u->id]['gross'] += (int) $t->gross_cost;
            $lv2Tree[$lv2Id]['rooms'][$roomId]['users'][$u->id]['net']   += (int) $t->net_cost;

            // cộng tổng room
            $lv2Tree[$lv2Id]['rooms'][$roomId]['gross'] += (int) $t->gross_cost;
            $lv2Tree[$lv2Id]['rooms'][$roomId]['net']   += (int) $t->net_cost;

            // cộng tổng lv2
            $lv2Tree[$lv2Id]['gross'] += (int) $t->gross_cost;
            $lv2Tree[$lv2Id]['net']   += (int) $t->net_cost;

            // tổng toàn sàn
            $grandGross += (int) $t->gross_cost;
            $grandNet   += (int) $t->net_cost;
        }
    }

    // đổi về collection cho dễ foreach
    $lv2Tree = collect($lv2Tree)->map(function ($lv2) {
        $lv2['rooms'] = collect($lv2['rooms'])->map(function ($room) {
            $room['users'] = collect($room['users']);
            return $room;
        });
        return $lv2;
    });

    return view('account.tasks', compact(
        'user',
        'reports',
        'selectedReportId',
        'departmentOptions',
        'lv2Tree',
        'grandGross',
        'grandNet'
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


    public function updateKpi(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'kpi' => 'nullable|string|max:255',
        ]);

        $task = Task::find($request->task_id);
        $task->kpi = $request->kpi;
        $task->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật KPI thành công'
        ]);
    }

    public function updateExpectedCost(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'expected_costs' => 'required|numeric|min:0',
        ]);

        $task = Task::find($request->task_id);
        $task->expected_costs = $request->expected_costs;
        $task->save();

        return response()->json([
            'status' => true,
            'expected_costs' => number_format($task->expected_costs, 0, ',', '.'),
            'raw_expected_costs' => $task->expected_costs,
        ]);

    }

}
