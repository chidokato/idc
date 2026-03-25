<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Report;
use App\Models\Task;
use Illuminate\Http\Request;

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
            ->havingRaw('COALESCE(SUM(tasks.actual_costs), 0) > 0')
            ->orderByDesc('total_actual_costs')
            ->get();

        $projectTotalActualCosts = (float) $projectSummaries->sum('total_actual_costs');

        return view('account.report.statistical', [
            'reports' => $reports,
            'departments' => $departments,
            'selectedReportIds' => $selectedReportIds,
            'summary' => $summary,
            'reportSummaries' => $reportSummaries,
            'projectSummaries' => $projectSummaries,
            'projectTotalActualCosts' => $projectTotalActualCosts,
        ]);
    }
}
