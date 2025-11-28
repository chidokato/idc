<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Report;
use Carbon\Carbon;

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

        // Lưu báo cáo
        Report::create([
            'name' => $request->name,
            'time_start' => $time_start,
            'time_end' => $time_end,
        ]);

        return response()->json(['success' => true, 'message' => 'Đã lưu báo cáo']);
    }

    public function loadReport()
    {
        $reports = Report::orderBy('created_at','desc')->get();
        return view('account.layout.load_report', compact('reports'));
    }

    public function show($id)
    {
        $report = Report::find($id);
        $days = Carbon::parse($report->time_start)->diffInDays(Carbon::parse($report->time_end)) + 1;
        $task = Task::with([
            'User.department.parent.parent', // lấy department + 2 cấp cha
            'Post',
            'Channel'
        ])->where('report_id', $id)->get();
        return view('account.showreport', compact('report', 'task', 'days'));
    }


    public function update(Request $request)
    {
        $dates = explode(' - ', $request->date);

        Report::where('id', $request->id)->update([
            'name' => $request->name,
            'time_start' => Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d'),
            'time_end'   => Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d'),
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




}
