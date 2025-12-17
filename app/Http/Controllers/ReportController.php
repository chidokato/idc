<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Report;
use App\Models\Post;
use App\Models\Channel;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\TreeHelper;


class ReportController extends HomeController
{
    public function index()
    {
        $reports = Report::get();
        return view('account.report', compact('reports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'date' => 'required|string',
        ]);

        // Tách khoảng thời gian
        $dates = explode(' - ', $request->date); // ["01/11/2025", "15/11/2025"]
        $time_start = isset($dates[0]) ? \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d') : null;
        $time_end   = isset($dates[1]) ? \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d') : null;
        $days = Carbon::parse($time_start)->diffInDays(Carbon::parse($time_end)) + 1;
        // Lưu báo cáo
        Report::create([
            'name' => $request->name,
            'time_start' => $time_start,
            'time_end' => $time_end,
            'days' => $days,
        ]);

        return response()->json(['success' => true, 'message' => 'Đã lưu báo cáo']);
    }

    public function loadReport()
    {
        $reports = Report::orderBy('created_at','desc')->get();
        return view('account.layout.load_report', compact('reports'));
    }

    public function show(Request $request, $id)
    {
        $report = Report::find($id);

        // $task = Task::all();
        // foreach($task as $t){
        //     if ($t->approved == null) {
        //         $task = Task::find($t->id);
        //         $task->approved = '0';
        //         $task->save();
        //     }
        // }

        if (!$report) {
            abort(404, 'Report not found');
        }

        $days = Carbon::parse($report->time_start)->diffInDays(Carbon::parse($report->time_end)) + 1;

        // Base query
        $query = Task::with([
            'User.department.parent.parent', // giữ nguyên quan hệ cũ
            'Post',
            'Channel'
        ])
        ->where('report_id', $id);
       
        if ($request->department_id) {
            $departmentIds = Department::getChildIds($request->department_id);
            $query->whereHas('department', function($q) use ($departmentIds) {
                $q->whereIn('id', $departmentIds);
            });

        }

        if ($request->post_id) {
            $query->where('post_id', $request->post_id);
        }

        if ($request->channel_id) {
            $channelIds = Channel::getChildIds($request->channel_id); // lấy tất cả con
            $query->whereIn('channel_id', $channelIds);
        }

        if ($request->filled('approved')) {
            $query->where('approved', $request->approved);
        }


        $task = $query->paginate(1000)->appends($request->query());

        // Select filter
        $departments = Department::all();
        $departmentOptions = TreeHelper::buildOptions($departments,0,'',$request->department_id);

        $posts       = Post::where('sort_by', 'Product')->where('rate', '!=', Null)->get();
        $channels    = Channel::all();
        $channelsOptions = TreeHelper::buildOptions($channels,0,'',$request->channel_id);

        $tongTien = $task->sum(function ($val) {
            return $val->total_costs ?? ($val->days * $val->expected_costs);
        });

        return view('account.showreport', compact(
            'report',
            'task',
            'days',
            'departmentOptions',
            'posts',
            'channelsOptions',
            'tongTien'
        ));
    }



    public function update(Request $request)
    {
        $dates = explode(' - ', $request->date);

        $start = Carbon::createFromFormat('d/m/Y', $dates[0]);
        $end   = Carbon::createFromFormat('d/m/Y', $dates[1]);

        $days = $start->diffInDays($end) + 1; // tính cả 2 ngày

        Report::where('id', $request->id)->update([
            'name' => $request->name,
            'time_start' => $start->format('Y-m-d'),
            'time_end'   => $end->format('Y-m-d'),
            'days' => $days,
        ]);

        // return back()->with('success', 'Đã lưu thành công!');
        return response()->json(['success' => true]);
    }



    public function delete(Request $request)
    {
        Report::where('id', $request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function active(Request $request)
    {
        Report::where('id', $request->id)->update([
            'active' => $request->active
        ]);

        return response()->json(['success' => true]);
    }

    public function payment(Request $request, $id)
    {
        $query = Task::where('report_id', $id)
            ->select(
                'department_lv1',
                'department_lv2',
                'department_id',
                'user_id',

                // ✅ Chi phí dự kiến
                DB::raw('SUM(COALESCE(days,0) * COALESCE(expected_costs,0)) as gross_cost'),

                // ✅ Chi phí ròng (sau hỗ trợ)
                DB::raw('SUM(
                    COALESCE(days,0)
                    * COALESCE(expected_costs,0)
                    * (1 - COALESCE(rate,0)/100)
                ) as net_cost'),

                // ✅ Chi phí thực tế
                DB::raw('SUM(COALESCE(actual_costs,0)) as actual_cost')
            )
            ->with(['user', 'department']);

        // 🔎 Filter (giữ nguyên)
        if ($request->filled('department_lv1')) {
            $query->where('department_lv1', $request->department_lv1);
        }

        if ($request->filled('department_lv2')) {
            $query->where('department_lv2', $request->department_lv2);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $summary = $query
            ->groupBy(
                'department_lv1',
                'department_lv2',
                'department_id',
                'user_id'
            )
            ->get()
            ->map(function ($item) {
                // ✅ Tiền hỗ trợ
                $item->support_cost = $item->gross_cost - $item->net_cost;
                return $item;
            });

        return view('account.report.payment', compact('summary'));
    }




}
