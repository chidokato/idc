<?php

namespace App\Http\Controllers\Account;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TaskCostPeriod;
use App\Models\Report; // nếu bạn có model Report

class TaskCostPeriodController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách report để dropdown
        // Nếu table report tên khác, bạn sửa lại model/logic ở đây.
        $reports = Report::orderByDesc('id')->get();

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

        return view('account.index', [
            'reports'  => $reports,
            'reportId' => $reportId,
            'groupBy'  => $groupBy,
            'rows'     => $rows,
        ]);
    }

    /**
     * Rebuild tổng hợp cho 1 report_id từ bảng tasks.
     * - UPSERT vào task_cost_period
     * - sum_actual = SUM(COALESCE(actual_costs,0))
     *   (Nếu bạn muốn net: actual + extra - refund, nói mình sửa)
     */
    public function rebuild(Request $request)
    {
        $reportId = (string) $request->report_id;
        if (!$reportId) {
            return redirect()->back()->with('error', 'Thiếu report_id');
        }

        // Lấy period_start/period_end từ bảng report (nếu có)
        // Nếu report table của bạn khác cấu trúc, sửa tại đây.
        $report = Report::find($reportId);
        if (!$report) {
            return redirect()->back()->with('error', 'Không tìm thấy report');
        }

        // Giả định report có start_date/end_date (date)
        // Nếu report của bạn lưu kiểu khác, bạn map lại.
        $start = $report->start_date; // '2026-01-01'
        $end   = $report->end_date;   // '2026-01-15' hoặc '2026-01-31'

        // Tính year_month + period_no (tùy bạn dùng, vẫn lưu cho rõ)
        $yearMonth = date('Y-m', strtotime($start));
        $dayStart  = (int) date('d', strtotime($start));
        $periodNo  = ($dayStart <= 15) ? 1 : 2;

        // Xóa dữ liệu cũ của report này trước (an toàn, tránh sót)
        DB::table('task_cost_period')->where('report_id', $reportId)->delete();

        // Insert lại từ tasks
        // Nếu bạn muốn chỉ approved=1, active=1... bạn thêm WHERE ở dưới.
        DB::table('task_cost_period')->insertUsing(
            [
                'year_month',
                'period_no',
                'period_start',
                'period_end',
                'report_id',
                'department_id',
                'department_lv1',
                'department_lv2',
                'user_id',
                'channel_id',
                'sum_actual',
                'last_calc_at',
                'created_at',
                'updated_at',
            ],
            DB::table('tasks')
                ->selectRaw("
                    ? as year_month,
                    ? as period_no,
                    ? as period_start,
                    ? as period_end,
                    report_id,
                    department_id,
                    department_lv1,
                    department_lv2,
                    user_id,
                    channel_id,
                    SUM(COALESCE(actual_costs,0)) as sum_actual,
                    NOW() as last_calc_at,
                    NOW() as created_at,
                    NOW() as updated_at
                ", [$yearMonth, $periodNo, $start, $end])
                ->where('report_id', $reportId)
                ->where('approved', 1)
                // ->where('active', '1') // nếu bạn muốn
                ->groupBy([
                    'report_id',
                    'department_id',
                    'department_lv1',
                    'department_lv2',
                    'user_id',
                    'channel_id',
                ])
        );

        return redirect()->route('task_cost_period.index', [
            'report_id' => $reportId,
            'group_by' => $request->get('group_by', 'full'),
        ])->with('success', 'Rebuild thành công cho kỳ: ' . $reportId);
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
