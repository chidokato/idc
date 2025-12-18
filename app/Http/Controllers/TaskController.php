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
        $selectedReportId = (int) $request->input('report_id', optional($reports->first())->id);

        $departments = Department::orderBy('name')->get();

        // dropdown đang là LV1+LV2 => id có thể là lv1 hoặc lv2
        $selectedDeptId = (int) $request->input('department_id', $user->department_lv2);

        // build options (dùng hàm của bạn)
        $departmentOptions = TreeHelperLv2Only::buildOptions($departments, $selectedDeptId);

        // ===== xác định danh sách LV2 cần lọc =====
        $selectedDept = $departments->firstWhere('id', $selectedDeptId);

        $lv2Ids = [];
        if ($selectedDept) {
            if ((int)$selectedDept->parent === 0) {
                // chọn LV1 => lấy toàn bộ LV2 con
                $lv2Ids = $departments->where('parent', $selectedDeptId)->pluck('id')->map(fn($x)=>(int)$x)->all();
            } else {
                // chọn LV2
                $lv2Ids = [$selectedDeptId];
            }
        } else {
            // fallback: sàn của user
            $lv2Ids = [(int)$user->department_lv2];
        }

        // ===== lấy users + tasks theo report =====
        $users = User::query()
            ->whereIn('department_lv2', $lv2Ids)
            ->orderBy('department_lv2')
            ->orderBy('department_id')
            ->with([
                'tasks' => function ($q) use ($selectedReportId) {
                    $q->where('approved', 1)
                      ->when($selectedReportId, fn($qq) => $qq->where('report_id', $selectedReportId))
                      ->with(['department', 'Post', 'Channel', 'Report']);
                }
            ])
            ->get();

        // ===== build cây + tổng: LV1 -> LV2 -> ROOM -> USER -> TASK =====
        $tree = [];
        $grandGross = 0;
        $grandNet   = 0;

        foreach ($users as $u) {
            foreach ($u->tasks as $t) {

                $lv2Id = (int)$u->department_lv2;
                $lv2   = $departments->firstWhere('id', $lv2Id);
                $lv1Id = (int)($lv2?->parent ?? 0);
                $lv1   = $departments->firstWhere('id', $lv1Id);

                $lv1Name = $lv1?->name ?? ('Cty #' . $lv1Id);
                $lv2Name = $lv2?->name ?? ('Sàn #' . $lv2Id);

                $roomId   = (int)($t->department_id ?? 0);
                $roomName = $t->department?->name
                    ?? $departments->firstWhere('id', $roomId)?->name
                    ?? ('Phòng #' . $roomId);

                // init lv1
                if (!isset($tree[$lv1Id])) {
                    $tree[$lv1Id] = ['id'=>$lv1Id,'name'=>$lv1Name,'gross'=>0,'net'=>0,'lv2s'=>[]];
                }

                // init lv2
                if (!isset($tree[$lv1Id]['lv2s'][$lv2Id])) {
                    $tree[$lv1Id]['lv2s'][$lv2Id] = ['id'=>$lv2Id,'name'=>$lv2Name,'gross'=>0,'net'=>0,'rooms'=>[]];
                }

                // init room
                if (!isset($tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId])) {
                    $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId] = ['id'=>$roomId,'name'=>$roomName,'gross'=>0,'net'=>0,'users'=>[]];
                }

                // init user node
                if (!isset($tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['users'][$u->id])) {
                    $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['users'][$u->id] = [
                        'id'=>$u->id,
                        'employee_code'=>$u->employee_code,
                        'yourname'=>$u->yourname,
                        'gross'=>0,'net'=>0,
                        'tasks'=>[]
                    ];
                }

                // push task + totals
                $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['users'][$u->id]['tasks'][] = $t;

                $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['users'][$u->id]['gross'] += (int)$t->gross_cost;
                $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['users'][$u->id]['net']   += (int)$t->net_cost;

                $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['gross'] += (int)$t->gross_cost;
                $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['net']   += (int)$t->net_cost;

                $tree[$lv1Id]['lv2s'][$lv2Id]['gross'] += (int)$t->gross_cost;
                $tree[$lv1Id]['lv2s'][$lv2Id]['net']   += (int)$t->net_cost;

                $tree[$lv1Id]['gross'] += (int)$t->gross_cost;
                $tree[$lv1Id]['net']   += (int)$t->net_cost;

                $grandGross += (int)$t->gross_cost;
                $grandNet   += (int)$t->net_cost;
            }
        }

        $tree = collect($tree)->map(function ($lv1) {
            $lv1['lv2s'] = collect($lv1['lv2s'])->map(function ($lv2) {
                $lv2['rooms'] = collect($lv2['rooms'])->map(function ($room) {
                    $room['users'] = collect($room['users']);
                    return $room;
                });
                return $lv2;
            });
            return $lv1;
        });

        return view('account.tasks', compact(
            'user',
            'reports',
            'selectedReportId',
            'departmentOptions',
            'tree',
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


    public function updatePaid(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->rank, [1, 2])) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $task = Task::findOrFail($id);

        $task->paid = !$task->paid;
        $task->save();

        return response()->json([
            'success' => true,
            'paid' => (bool) $task->paid,
            'message' => $task->paid ? 'Chuyển sang trạng thái: Đã thanh toán' : 'Chuyển sang trạng thái: Chưa thanh toán',
        ]);
    }

}
