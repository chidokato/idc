<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Services\WalletService;
use App\Models\User;
use App\Models\Department;
use App\Models\Wallet;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TreeHelperLv2Only;
use App\Helpers\TreeHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;

class TaskController extends Controller
{
    public function show(Request $request, Task $task = null)
    {
        // nếu bạn không dùng show chi tiết task, cho nó chạy như trang danh sách
        return $this->tasksuser($request);
    }
    

    public function tasksuser(Request $request)
    {
        $user = Auth::user();

        // Load departments 1 lần để: (1) build options (2) lấy danh sách con cháu nhanh
        $departments = Department::select('id', 'parent', 'name')
            ->orderBy('name')
            ->get();

        // selected department: ưu tiên request, không có thì lấy department_lv2 của user
        $selectedDeptId = (int) $request->input('department_id', $user->department_lv2 ?? 0);
        if ($selectedDeptId <= 0) {
            $selectedDeptId = (int) ($user->department_lv2 ?? 0);
        }

        // report filter (nếu có)
        $reportId = (int) $request->input('report_id', 0);

        // Lấy tất cả id con cháu + chính nó (KHÔNG N+1 query)
        $deptIds = [];
        if ($selectedDeptId > 0) {
            $deptIds = $this->getChildIdsFromCollection($departments, $selectedDeptId);
        }
        $approved = $request->input('approved', null); // '1' | '0' | null

        // Query tasks theo department_id IN (...)
        $q = Task::query()
            ->with(['handler', 'department', 'Post', 'channel'])
            ->orderByDesc('department_lv2');

        if (!empty($deptIds)) {
            $q->whereIn('department_id', $deptIds);
        } else {
            // nếu không xác định được phòng ban => trả rỗng
            $q->whereRaw('1=0');
        }

        // nếu tasks có cột report_id thì mới lọc
        if ($reportId > 0 && Schema::hasColumn('tasks', 'report_id')) {
            $q->where('report_id', $reportId);
        }
        
        // lọc approved nếu có chọn
        if ($approved !== null && $approved !== '') {
            $q->where('approved', (int) $approved);
        }

        $tasks = $q->get();

        // tính tổng
        $sumTotal = 0;
        $sumPaid  = 0;

        foreach ($tasks as $task) {
            $rowTotal = $task->expected_costs * $task->days;
            $rowPaid  = $rowTotal * (1 - $task->rate / 100);

            $sumTotal += $rowTotal;
            $sumPaid  += $rowPaid;
        }

        // ===== AJAX: chỉ trả tbody rows =====
        if ($request->ajax()) {
            $html = view('account.task.partials.task_rows', compact('tasks'))->render();

            return response()->json([
              'html' => $html,
              'sumTotal' => $sumTotal,
              'sumPaid' => $sumPaid,
            ]);
        }

        // ===== Render trang =====
        $reports = Report::orderByDesc('id')->get();

        $departmentOptions = TreeHelper::buildOptions(
            $departments,
            0,
            '',
            $selectedDeptId,
            'id',
            'parent',
            'name'
        );


        return view('account.task.taskuser', compact(
            'user',
            'tasks',
            'departmentOptions',
            'selectedDeptId',
            'reports',
            'reportId',
            'sumTotal',
            'sumPaid'
        ));
    }

    private function moneyToFloat($v): float
    {
        $s = (string) ($v ?? '');
        $s = preg_replace('/[^\d\-]/', '', $s); // chỉ giữ số và dấu -
        return (float) ($s ?: 0);
    }

    /**
     * Số thường (days, rate): cho phép "10,5" -> 10.5
     */
    private function numToFloat($v): float
    {
        $s = (string) ($v ?? '');
        $s = str_replace(',', '.', $s);
        $s = preg_replace('/[^0-9\.\-]/', '', $s);
        return (float) ($s ?: 0);
    }

    /**
     * Lấy tất cả id con cháu + chính nó từ collection departments (không query thêm)
     */
    private function getChildIdsFromCollection($departments, int $rootId): array
    {
        $childrenMap = [];

        foreach ($departments as $d) {
            $p = (int) ($d->parent ?? 0);
            $childrenMap[$p][] = (int) $d->id;
        }

        $ids = [$rootId];
        $stack = [$rootId];

        while (!empty($stack)) {
            $cur = array_pop($stack);
            foreach (($childrenMap[$cur] ?? []) as $childId) {
                $ids[] = $childId;
                $stack[] = $childId;
            }
        }

        return array_values(array_unique($ids));
    }

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
                'wallet:id,user_id,balance,held_balance', // ✅ thêm dòng này
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
                        'id' => $u->id,
                        'employee_code' => $u->employee_code,
                        'yourname' => $u->yourname,
                        'gross' => 0,
                        'net' => 0,
                        'tasks' => [],

                        'wallet' => [
                            'balance' => (float) optional($u->wallet)->balance ?? 0,
                            'held_balance' => (float) optional($u->wallet)->held_balance ?? 0,
                        ],
                    ];
                }

                $gross = (int) round($t->gross_cost, 0);
                $net   = (int) round($t->net_cost, 0);

                // push task
                $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['users'][$u->id]['tasks'][] = $t;

                // USER subtotal
                $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['users'][$u->id]['gross'] += $gross;
                $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['users'][$u->id]['net']   += $net;

                // ROOM subtotal
                $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['gross'] += $gross;
                $tree[$lv1Id]['lv2s'][$lv2Id]['rooms'][$roomId]['net']   += $net;

                // LV2 subtotal
                $tree[$lv1Id]['lv2s'][$lv2Id]['gross'] += $gross;
                $tree[$lv1Id]['lv2s'][$lv2Id]['net']   += $net;

                // LV1 subtotal
                $tree[$lv1Id]['gross'] += $gross;
                $tree[$lv1Id]['net']   += $net;

                // grand total
                $grandGross += $gross;
                $grandNet   += $net;
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

public function updatePaid(Request $request, Task $task, WalletService $walletService)
{
    abort_unless(auth()->check(), 403);

    $rank = (int) auth()->user()->rank;
    $meId = (int) auth()->id();
    $paid = (int) $request->input('paid', 0);

    // LV3 = department_id
    $myDept   = (int) (auth()->user()->department_id ?? 0);
    $taskDept = (int) ($task->department_id ?? 0);
    $sameDept = ($myDept !== 0 && $taskDept !== 0 && $myDept === $taskDept);

    // cột user trong task (ID user sử dụng task)
    $taskUserId = (int) ($task->user ?? 0);
    $isMine = ($taskUserId === $meId);

    

    // Rank 1: full
    if ($rank === 1) {
        // ok
    }
    // Rank 2: chỉ HOLD nếu cùng department_id, không RELEASE
    else if ($rank === 2) {
        if ($paid === 0) {
            return response()->json(['status' => false, 'message' => 'Rank 2 không được hủy giữ tiền.'], 403);
        }
        if (!$sameDept) {
            return response()->json(['status' => false, 'message' => 'Rank 2 chỉ được giữ tiền cho tác vụ cùng phòng ban (department).'], 403);
        }
    }
    // Rank 3: chỉ HOLD task của mình, không RELEASE
    else if ($rank === 3) {
        if ($paid === 0) {
            return response()->json(['status' => false, 'message' => 'Rank 3 không được hủy giữ tiền.'], 403);
        }
        if (!$isMine) {
            return response()->json(['status' => false, 'message' => 'Bạn chỉ được giữ tiền cho tác vụ của mình.'], 403);
        }
    }
    else {
        return response()->json(['status' => false, 'message' => 'Bạn không có quyền thao tác.'], 403);
    }

    try {
        if ($paid === 1) {
            $walletService->holdTask($task);
            // sô tiền trong ví
            $wallet = Wallet::where('user_id', $meId)->first();

            return response()->json([
                'status' => true, 
                'message' => 'Đã giữ tiền (HOLD) thành công.',
                'wallet' => [
                      'balance' => (string)($wallet->balance ?? '0.00'),
                      'held_balance' => (string)($wallet->held_balance ?? '0.00'),
                      'total' => (string)bcadd((string)($wallet->balance ?? '0.00'), (string)($wallet->held_balance ?? '0.00'), 2),
                  ]
            ]);
        } else {
            $walletService->releaseTask($task, 'admin_toggle_off');
            // sô tiền trong ví
            $wallet = Wallet::where('user_id', $meId)->first();
            return response()->json([
                'status' => true, 
                'message' => 'Đã nhả giữ tiền (RELEASE) thành công.',
                'wallet' => [
                      'balance' => (string)($wallet->balance ?? '0.00'),
                      'held_balance' => (string)($wallet->held_balance ?? '0.00'),
                      'total' => (string)bcadd((string)($wallet->balance ?? '0.00'), (string)($wallet->held_balance ?? '0.00'), 2),
                  ]
            ]);
        }
    } catch (ValidationException $e) {
        $first = collect($e->errors())->flatten()->first() ?? 'Dữ liệu không hợp lệ.';
        return response()->json(['status' => false, 'message' => $first, 'errors' => $e->errors()], 422);
    } catch (\Throwable $e) {
        return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
    }
}



    public function bulkUpdateTasks(Request $request)
    {
        $rateKeys = array_keys(config('rates', []));

        $data = $request->validate([
            'ids' => ['required','array','min:1'],
            'ids.*' => ['integer','distinct','exists:tasks,id'],

            'apply_expected' => ['nullable','boolean'],
            'expected_costs' => ['nullable','integer','min:0','required_if:apply_expected,1'],

            'apply_rate' => ['nullable','boolean'],
            'rate' => ['nullable', 'required_if:apply_rate,1', 'in:'.implode(',', $rateKeys)],

            'apply_approved' => ['nullable','boolean'],
            'approved_action' => ['nullable','required_if:apply_approved,1','in:approve,unapprove'],
        ]);

        $ids = $data['ids'];

        $updatedRows = [];

        DB::transaction(function () use ($data, $ids, &$updatedRows) {
            $tasks = Task::whereIn('id', $ids)->get();

            foreach ($tasks as $t) {
                if (!empty($data['apply_expected'])) {
                    $t->expected_costs = (int)$data['expected_costs'];
                    // nếu bạn muốn đồng bộ total_costs luôn:
                    $t->total_costs = (int)$t->days * (int)$t->expected_costs;
                }

                if (!empty($data['apply_rate'])) {
                    $t->rate = $data['rate'];
                }

                if (!empty($data['apply_approved'])) {
                    $t->approved = ($data['approved_action'] === 'approve') ? 1 : 0;
                }

                $t->save();

                $updatedRows[] = [
                    'id' => $t->id,
                    'expected_costs' => (int)$t->expected_costs,
                    'rate' => (string)$t->rate,
                    'approved' => (int)$t->approved,
                    'total_costs' => (int)($t->total_costs ?? ($t->days * $t->expected_costs)),
                ];
            }
        });

        return response()->json([
            'message' => 'Đã cập nhật hàng loạt thành công!',
            'rows' => $updatedRows,
        ]);
    }
}
