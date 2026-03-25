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

        $baseDepartmentQuery = Task::query()
            ->leftJoin('departments as current_department', 'current_department.id', '=', 'tasks.department_id')
            ->leftJoin('departments as parent_department', 'parent_department.id', '=', 'current_department.parent')
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

        $departmentSummaries = (clone $baseDepartmentQuery)
            ->selectRaw('tasks.department_id')
            ->selectRaw('COALESCE(current_department.name, "Khong xac dinh") as department_name')
            ->selectRaw('COALESCE(parent_department.name, "") as parent_department_name')
            ->selectRaw('COUNT(tasks.id) as total_tasks')
            ->selectRaw('COUNT(DISTINCT tasks.report_id) as total_reports')
            ->selectRaw('COALESCE(SUM(tasks.actual_costs), 0) as total_actual_costs')
            ->groupBy('tasks.department_id', 'current_department.name', 'parent_department.name')
            ->havingRaw('COALESCE(SUM(tasks.actual_costs), 0) > 0')
            ->orderByDesc('total_actual_costs')
            ->get();

        $departmentTotalActualCosts = (float) $departmentSummaries->sum('total_actual_costs');

        return view('account.report.statistical', [
            'reports' => $reports,
            'departments' => $departments,
            'selectedReportIds' => $selectedReportIds,
            'summary' => $summary,
            'projectSummaries' => $projectSummaries,
            'projectTotalActualCosts' => $projectTotalActualCosts,
            'departmentSummaries' => $departmentSummaries,
            'departmentTotalActualCosts' => $departmentTotalActualCosts,
        ]);
    }
}
