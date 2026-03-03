<?php

namespace App\Http\Controllers\Account;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use App\Models\TaskCostPeriod;
use App\Models\Task;
use App\Models\Report; // nếu bạn có model Report
use Illuminate\Support\Carbon;

class TaskCostPeriodController extends Controller
{
    public function index(Request $request)
    {
        $duan_idc = TaskCostPeriod::get();
        // Lấy danh sách report để dropdown
        // Nếu table report tên khác, bạn sửa lại model/logic ở đây.
        $reports = Report::orderByDesc('id')->get();

        $departments = Department::where('parent', 0)->get();

        $reportId = $request->filled('report_id')
            ? (string) $request->report_id
            : ($reports->first() ? (string) $reports->first()->id : null);

        $groupBy = $request->get('group_by', 'full'); // san|san_phong|san_phong_nhom|user|channel|full

        $groupCols = $this->groupCols($groupBy);

        $rows = collect();
        if ($reportId) {
            $rows = TaskCostPeriod::query()
                ->where('report_id', $reportId)
                ->select($groupCols)
                ->selectRaw('SUM(sum_actual) as total_actual')
                ->groupBy($groupCols)
                ->orderByDesc('total_actual')
                ->paginate(50)
                ->withQueryString();
        }

        return view('account.report.statistical', [
            'reports'  => $reports,
            'reportId' => $reportId,
            'groupBy'  => $groupBy,
            'rows'     => $rows,
            'departments'     => $departments,
            'duan_idc'     => $duan_idc,
        ]);
    }

    /**
     * Rebuild tổng hợp cho 1 report_id từ bảng tasks.
     * - UPSERT vào task_cost_period
     * - sum_actual = SUM(COALESCE(actual_costs,0))
     *   (Nếu bạn muốn net: actual + extra - refund, nói mình sửa)
     */
    

    public function updateMonthly(Request $request)
    {
        $departmentId = $request->department_id;
        $reportIds    = $request->report_id; // mảng
        
        $rows = Task::query()
        ->where('actual_costs', '>', 0)
        ->when($departmentId, fn($q) => $q->where('department_lv1', $departmentId))
        ->when($reportIds, fn($q) => $q->whereIn('report_id', $reportIds))
        ->selectRaw('post_id, SUM(actual_costs) as total_cost')
        ->groupBy('post_id')
        ->get();

        // Chuẩn bị data để upsert
        $now = now();
        $upsertData = $rows->map(function ($r) use ($now, $departmentId, $reportIds) {
            return [
                'post_id' => (int) $r->post_id,
                'total_cost' => (float) $r->total_cost,
                'department_lv1' => $departmentId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        DB::table('task_cost_period')->upsert(
            $upsertData,
            ['post_id'],                 // key chống trùng
            ['total_cost'] // fields update khi đã tồn tại
        );

    }

    /**
     * Trả về 2 kỳ trong tháng:
     * - period_no=1: 01-15
     * - period_no=2: 16-lastday
     */
    protected function buildPeriods(int $year, int $month): array
    {
        $start1 = Carbon::create($year, $month, 1)->toDateString();
        $end1   = Carbon::create($year, $month, 15)->toDateString();

        $start2 = Carbon::create($year, $month, 16)->toDateString();
        $end2   = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        return [
            ['period_no' => 1, 'start' => $start1, 'end' => $end1],
            ['period_no' => 2, 'start' => $start2, 'end' => $end2],
        ];
    }


    private function groupCols($groupBy)
    {
        switch ($groupBy) {
            case 'san':
                return ['department_id'];
            case 'san_phong':
                return ['department_id', 'department_lv1'];
            case 'san_phong_nhom':
                return ['department_id', 'department_lv1', 'department_lv2'];
            case 'user':
                return ['user_id'];
            case 'channel':
                return ['channel_id'];
            case 'full':
            default:
                return ['department_id', 'department_lv1', 'department_lv2', 'user_id', 'channel_id'];
        }
    }
}
