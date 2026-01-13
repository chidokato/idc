<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Services\WalletService;
use App\Models\User;
use App\Models\Department;
use App\Models\Wallet;
use App\Models\Report;
use App\Models\Post;
use App\Models\Channel;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TreeHelperLv2Only;
use App\Helpers\TreeHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;

use App\Services\TaskFinanceService;

class ExpenseController extends Controller
{
    public function expense(Request $request){
        // $tasks = Task::get();
        $tasks = Task::where('user', auth()->id())->get();
        
        // foreach ($tasks as $task) {
        //     $approved = (int) ($task->approved ?? 0);
        //     $paid     = (int) ($task->paid ?? 0);

        //     // Không duyệt hoặc chưa trả: extra = actual, refund = 0
        //     if ($approved !== 1 || $paid !== 1) {
        //         $task->extra_money  = (float) ($task->actual_costs ?? 0);
        //         $task->refund_money = 0;
        //         $task->save();
        //         continue;
        //     }

        //     // approved = 1 và paid = 1
        //     $actual   = (float) ($task->actual_costs ?? 0);
        //     $expected = (float) (($task->expected_costs ?? 0) * ($task->days ?? 0));
        //     $rate     = (float) ($task->rate ?? 0);

        //     if ($actual > $expected) {
        //         $task->extra_money  = $actual - $expected;
        //         $task->refund_money = 0;
        //     } else {
        //         // tiền giữ lại (hold) = (expected - actual) * (1 - rate/100)
        //         $diff = $expected - $actual; // luôn >= 0
        //         $task->extra_money  = 0;
        //         $task->refund_money = $diff * (1 - $rate / 100);
        //     }

        //     $task->save();
        // }



        // Tổng theo trang hiện tại
        $sumTotal = $tasks->sum(function ($t) {
            return (float)($t->expected_costs ?? 0) * (float)($t->days ?? 0);
        });

        $sumActual = $tasks->sum(function ($t) {
            return (float)($t->actual_costs ?? 0);
        });

        $sumPaid = $tasks->sum(function ($t) {
            $total = (float)($t->expected_costs ?? 0) * (float)($t->days ?? 0);
            $rate  = (float)($t->rate ?? 0);
            return $total * (1 - $rate/100);
        });
        // tìm kiếm
        $reports = Report::orderByDesc('id')->get();
        $users = User::get();
        $posts = Post::where('sort_by', 'Product')->get();
        $channels = Channel::get();
        $selectedReportId = $request->has('report_id')
            ? ($request->filled('report_id') ? (int)$request->report_id : null)
            : (int)Report::max('id');

        return view('account.task.expense', compact(
            'tasks',
            'sumTotal',
            'sumActual',
            'sumPaid',

            //tìm kiếm
            'users',
            'reports',
            'posts',
            'channels',
            'selectedReportId',
        ));
    }
    public function actualcosts(Request $request)
    {
        $user = auth()->user();
        $maxReportId = Report::max('id'); // có thể null nếu chưa có report
        $selectedReportId = $request->has('report_id')
            ? ($request->filled('report_id') ? (int)$request->report_id : null)
            : (int)$maxReportId;
        // Load departments 1 lần để build options + lọc đệ quy
        $departments = Department::select('id', 'parent', 'name')
            ->orderBy('name')
            ->get();

        // selected department: ưu tiên request, không có thì lấy department_id của user
        $selectedDeptId = $request->filled('department_id')
            ? (int) $request->department_id
            : null; // hoặc 0

        $q = Task::query()
            ->with(['handler', 'department', 'Post', 'channel'])
            ->orderBy('department_lv1')   // ưu tiên 1
            ->orderBy('department_lv2')   // ưu tiên 1
            ->orderBy('department_id')       // ưu tiên 2
            ->orderBy('user')          // ưu tiên 3 (hoặc user_id)
            ->orderByDesc('id');             // phụ: cho ổn định

        // Tìm theo mã NV / tên NV
        if ($request->filled('name')) {
            $key = trim($request->name);

            $q->whereHas('handler', function ($h) use ($key) {
                $h->where('employee_code', 'like', "%{$key}%")
                  ->orWhere('yourname', 'like', "%{$key}%");
            });
        }

        // Lọc phòng/nhóm (đệ quy: gồm cả con cháu)
        if ($request->filled('department_id')) {
            $deptId = (int) $request->department_id;

            // lấy danh sách id con cháu + chính nó
            $deptIds = TreeHelper::descendantIds($departments, $deptId, true);

            $q->whereIn('department_id', $deptIds);
        }

        // Lọc report
        if (!empty($selectedReportId)) {
            $q->where('report_id', $selectedReportId);
        }
        // if ($request->filled('report_id')) {
        //     $q->where('report_id', (int)$request->report_id);
        // }

        $tasks = $q->get();

        // Tổng theo trang hiện tại
        $sumTotal = $tasks->sum(function ($t) {
            return (float)($t->expected_costs ?? 0) * (float)($t->days ?? 0);
        });

        $sumActual = $tasks->sum(function ($t) {
            return (float)($t->actual_costs ?? 0);
        });

        $sumPaid = $tasks->sum(function ($t) {
            $total = (float)($t->expected_costs ?? 0) * (float)($t->days ?? 0);
            $rate  = (float)($t->rate ?? 0);
            return $total * (1 - $rate/100);
        });

        // Render filter options
        $reports = Report::orderByDesc('id')->get();
        $users = User::get();
        $posts = Post::where('sort_by', 'Product')->get();
        $channels = Channel::get();

        // selected option ưu tiên request('department_id') nếu có, fallback user dept
        // $selectedForOptions = $request->filled('department_id')
        //     ? (int)$request->department_id
        //     : (int)($user->department_id ?? 0);

        $departmentOptions = TreeHelper::buildOptions(
            $departments,
            0,
            '',
            $selectedDeptId,
            'id',
            'parent',
            'name'
        );

        return view('account.task.actualcosts', compact(
            'reports',
            'departmentOptions',
            'tasks',
            'sumTotal',
            'sumActual',
            'sumPaid',
            'selectedReportId',
            'users',
            'posts',
            'channels',
        ));
    }


    public function ajaxUpdateActualCosts(Request $request, Task $task)
    {
        // input có thể là "1.200.000" hoặc "1,200,000" => sanitize
        $raw = (string) $request->input('actual_costs', '');
        $clean = preg_replace('/[^\d\-]/', '', $raw); // giữ số và dấu -

        $data = $request->merge(['actual_costs' => $clean])->validate([
            'actual_costs' => ['required', 'numeric', 'min:0'],
        ]);

        $task->actual_costs = (int) $data['actual_costs'];
        $task->save();

        $paid = (int)($task->paid ?? 0);

        $expected = (float)($task->expected_costs ?? 0);
        $days     = (float)($task->days ?? 0);
        $rate     = (float)($task->rate ?? 0);

        $total  = $expected * $days;
        $actual = (float)$task->actual_costs;
        $hold   = $total * (1 - $rate / 100);

        $isCase2 = false;
        $isDanger = false;

        if ($paid !== 1) {
            // NEW RULE
            $diff = $actual;
            $isDanger = true;
        } else {
            if ($actual <= $total) {
                $diff = ($total - $actual) * (1 - $rate / 100);
            } else {
                $diff = ($actual - $total);
                $isCase2 = true;
                $isDanger = true;
            }
        }

        $diff = (int) round($diff);

        return response()->json([
            'ok' => true,
            'message' => 'Thành công',
            'task' => [
                'id' => $task->id,
                'paid' => $paid,
                'actual_costs' => (int)$task->actual_costs,
                'actual_costs_formatted' => number_format((int)$task->actual_costs, 0, ',', '.'),
                'diff' => $diff,
                'diff_formatted' => number_format($diff, 0, ',', '.'),
                'is_case2' => $isCase2,
                'is_danger' => $isDanger,
            ],
        ]);
    }

    public function ajaxSearchTasks(Request $request)
    {
        $q = Task::query()
            ->with(['handler', 'department', 'Post', 'channel'])
            ->orderByDesc('id');

        if ($request->filled('name')) {
            $key = trim($request->name);

            $q->whereHas('handler', function ($h) use ($key) {
                $h->where('employee_code', 'like', "%{$key}%")
                  ->orWhere('yourname', 'like', "%{$key}%");
            });
        }

        if ($request->filled('department_id')) {
            $q->where('department_id', $request->department_id);
        }

        if ($request->filled('report_id')) {
            $q->where('report_id', $request->report_id);
            // nếu là post_id thì đổi:
            // $q->where('post_id', $request->report_id);
        }

        $tasks = $q->paginate(20)->appends($request->query());

        $sumTotal = $tasks->getCollection()->sum(function ($t) {
            return (float)($t->expected_costs ?? 0) * (float)($t->days ?? 0);
        });

        $sumPaid = $tasks->getCollection()->sum(function ($t) {
            $total = (float)($t->expected_costs ?? 0) * (float)($t->days ?? 0);
            $rate  = (float)($t->rate ?? 0);
            return $total * (1 - $rate/100);
        });

        // !!! đổi đúng path partial của bạn:
        $tbodyHtml = view('account.task.partials._rows', compact('tasks'))->render();
        $paginationHtml = view('account.task.partials._pagination', compact('tasks'))->render();

        return response()->json([
            'ok' => true,
            'tbody_html' => $tbodyHtml,
            'pagination_html' => $paginationHtml,
            'sum_total' => number_format($sumTotal, 0, ',', '.'),
            'sum_paid'  => number_format($sumPaid, 0, ',', '.'),
            'has_rows'  => $tasks->count() > 0,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'        => ['required', 'integer', 'exists:users,id'],
            'post_id'        => ['required', 'integer', 'exists:posts,id'],
            'channel_id'     => ['required', 'integer', 'exists:channels,id'],
            'expected_costs' => ['required', 'integer', 'min:0'],
            'redirect_url'   => ['nullable', 'string'],
            'addreport_id'   => ['nullable', 'string'],
        ]);

        $user = User::find($data['user_id']);
        $post = Post::find($data['post_id']);
        $report = Report::find($data['addreport_id']);

        // TODO: map field đúng với DB của bạn (vd handler_id, Post_id...)
        $task = Task::create([
            'user_id'        => Auth::user()->id,
            'user'           => $data['user_id'],
            'report_id'      => $data['addreport_id'],
            'days'           => $report['days'],
            'department_lv1' => $user['department_lv1'],
            'department_lv2' => $user['department_lv2'],
            'department_id'  => $user['department_id'],
            'rate'           => $post->rate,
            'post_id'        => $data['post_id'],
            'channel_id'     => $data['channel_id'],
            'expected_costs' => $data['expected_costs'],
        ]);

        $redirect = $data['redirect_url'] ?: url()->previous();

        // Nếu submit bằng AJAX thì trả JSON để frontend redirect
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Đã thêm mới',
                'redirect' => $redirect,
                'id' => $task->id,
            ]);
        }

        // Submit thường
        return redirect()->to($redirect)->with('success', 'Đã thêm mới');
    }


}
