<?php

namespace App\Http\Controllers\Account;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TaskCostPeriod;
use App\Models\Report; // nếu bạn có model Report
use Illuminate\Support\Carbon;

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

        return view('account.report.statistical', [
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
    

    public function updateMonthly(Request $request, $note)
    {
        // note: post|san|nhom... (hiện bạn cần post)
        if ($note !== 'post') {
            return response()->json([
                'ok' => false,
                'message' => "Chưa hỗ trợ note = {$note} (hiện chỉ làm post)."
            ], 422);
        }

        $data = $request->validate([
            'year'  => ['required','integer','min:2000','max:2100'],
            'month' => ['required','integer','min:1','max:12'],
        ]);

        $year  = (int) $data['year'];
        $month = (int) $data['month'];

        // 2 kỳ: 1-15 và 16-cuối tháng
        $periods = $this->buildPeriods($year, $month);

        $now = now();
        $totalUpsert = 0;

        DB::beginTransaction();
        try {
            foreach ($periods as $p) {
                // Query tổng hợp theo post_id
                $rows = DB::table('tasks')
                    ->select([
                        DB::raw("'{$year}' as year"),
                        DB::raw("'{$month}' as month"),
                        DB::raw((int)$p['period_no']." as period_no"),
                        DB::raw("'{$p['start']}' as period_start"),
                        DB::raw("'{$p['end']}' as period_end"),
                        DB::raw("NULL as report_id"),
                        'post_id',
                        DB::raw('COALESCE(SUM(actual_costs),0) as sum_actual'),
                    ])
                    ->whereNotNull('post_id')
                    ->where('post_id', '!=', '')
                    // Giả định tasks có created_at để lọc theo kỳ
                    ->whereDate('created_at', '>=', $p['start'])
                    ->whereDate('created_at', '<=', $p['end'])
                    // chỉ lấy những task có actual_costs (tuỳ bạn bỏ dòng này)
                    ->whereNotNull('actual_costs')
                    ->groupBy('post_id')
                    ->get();

                // Upsert vào bảng tổng hợp
                foreach ($rows as $r) {
                    DB::table('task_cost_monthly')->updateOrInsert(
                        [
                            'year'       => (string)$year,     // cột year là char(7) nhưng bạn đang dùng "2026" => ok
                            'month'      => (string)$month,
                            'period_no'  => (int)$p['period_no'],
                            'post_id'    => $r->post_id,
                            'period_start' => $p['start'],
                            'period_end'   => $p['end'],
                        ],
                        [
                            'sum_actual'   => (float)$r->sum_actual,
                            'last_calc_at' => $now,
                            'updated_at'   => $now,
                            'created_at'   => $now, // updateOrInsert sẽ set luôn nếu insert
                        ]
                    );
                    $totalUpsert++;
                }
            }

            DB::commit();
            return response()->json([
                'ok' => true,
                'message' => "Đã tổng hợp chi phí theo dự án (post_id) cho {$month}/{$year}.",
                'rows_upserted' => $totalUpsert,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'ok' => false,
                'message' => 'Lỗi khi tổng hợp: '.$e->getMessage(),
            ], 500);
        }
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
