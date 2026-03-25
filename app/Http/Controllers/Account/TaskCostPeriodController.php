<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Report;
use App\Models\Task;
use App\Models\TaskCostPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TaskCostPeriodController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::orderByDesc('id')->get();
        $departments = Department::where('parent', 0)->get();

        $selectedReportIds = collect((array) $request->input('report_ids', []))
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($selectedReportIds->isEmpty() && $reports->isNotEmpty()) {
            $selectedReportIds = collect([(int) $reports->first()->id]);
        }

        $baseTaskQuery = Task::query()
            ->leftJoin('reports', 'reports.id', '=', 'tasks.report_id')
            ->leftJoin('posts', 'posts.id', '=', 'tasks.post_id')
            ->when(
                $selectedReportIds->isNotEmpty(),
                fn ($query) => $query->whereIn('tasks.report_id', $selectedReportIds->all())
            );

        $summary = (clone $baseTaskQuery)
            ->selectRaw('COUNT(tasks.id) as total_tasks')
            ->selectRaw('COUNT(DISTINCT tasks.post_id) as total_projects')
            ->selectRaw('COALESCE(SUM(tasks.actual_costs), 0) as total_actual_costs')
            ->selectRaw('COALESCE(SUM(tasks.extra_money), 0) as total_extra_money')
            ->selectRaw('COALESCE(SUM(tasks.refund_money), 0) as total_refund_money')
            ->first();

        $reportSummaries = (clone $baseTaskQuery)
            ->selectRaw('tasks.report_id')
            ->selectRaw('reports.name as report_name')
            ->selectRaw('reports.time_start as report_time_start')
            ->selectRaw('reports.time_end as report_time_end')
            ->selectRaw('COUNT(tasks.id) as total_tasks')
            ->selectRaw('COUNT(DISTINCT tasks.post_id) as total_projects')
            ->selectRaw('COALESCE(SUM(tasks.actual_costs), 0) as total_actual_costs')
            ->selectRaw('COALESCE(SUM(tasks.extra_money), 0) as total_extra_money')
            ->selectRaw('COALESCE(SUM(tasks.refund_money), 0) as total_refund_money')
            ->groupBy('tasks.report_id', 'reports.name', 'reports.time_start', 'reports.time_end')
            ->orderByDesc('tasks.report_id')
            ->get();

        $projectSummaries = (clone $baseTaskQuery)
            ->selectRaw('tasks.post_id')
            ->selectRaw('COALESCE(posts.name, "Khong xac dinh") as post_name')
            ->selectRaw('COUNT(tasks.id) as total_tasks')
            ->selectRaw('COUNT(DISTINCT tasks.report_id) as total_reports')
            ->selectRaw('COALESCE(SUM(tasks.actual_costs), 0) as total_actual_costs')
            ->selectRaw('COALESCE(SUM(tasks.extra_money), 0) as total_extra_money')
            ->selectRaw('COALESCE(SUM(tasks.refund_money), 0) as total_refund_money')
            ->groupBy('tasks.post_id', 'posts.name')
            ->orderByDesc('total_actual_costs')
            ->get();

        return view('account.report.statistical', [
            'reports' => $reports,
            'departments' => $departments,
            'selectedReportIds' => $selectedReportIds,
            'summary' => $summary,
            'reportSummaries' => $reportSummaries,
            'projectSummaries' => $projectSummaries,
        ]);
    }

    public function updateMonthly(Request $request)
    {
        $data = $request->validate([
            'department_id' => ['nullable', 'integer'],
            'report_id' => ['nullable', 'array'],
            'report_id.*' => ['integer'],
        ]);

        $departmentId = $data['department_id'] ?? null;
        $reportIds = $data['report_id'] ?? [];

        $rows = Task::query()
            ->where('actual_costs', '>', 0)
            ->when($departmentId, fn ($q) => $q->where('department_lv1', $departmentId))
            ->when($reportIds, fn ($q) => $q->whereIn('report_id', $reportIds))
            ->selectRaw('post_id, SUM(actual_costs) as total_cost')
            ->groupBy('post_id')
            ->get();

        $now = now();
        $upsertData = $rows->map(function ($r) use ($now, $departmentId) {
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
            ['post_id'],
            ['total_cost']
        );

        return redirect()
            ->route('task_cost_period.index', [
                'report_ids' => $reportIds,
            ])
            ->with('success', 'Đã cập nhật dữ liệu thống kê thành công.');
    }

    protected function buildPeriods(int $year, int $month): array
    {
        $start1 = Carbon::create($year, $month, 1)->toDateString();
        $end1 = Carbon::create($year, $month, 15)->toDateString();

        $start2 = Carbon::create($year, $month, 16)->toDateString();
        $end2 = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

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
