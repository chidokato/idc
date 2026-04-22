<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Mail\BulkPersonalMail;
use App\Models\Department;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class BulkMailController extends Controller
{
    public function create(Request $request)
    {
        $count = User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->count();

        $departments = Department::query()
            ->orderBy('name')
            ->get(['id', 'name', 'parent']);

        $users = User::query()
            ->with('department:id,name')
            ->select('id', 'name', 'yourname', 'email', 'department_id')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('yourname')
            ->orderBy('name')
            ->get();

        $batchId = $request->get('batch_id');
        $logs = collect();
        $stats = null;

        if ($batchId) {
            $logs = MailLog::query()
                ->where('batch_id', $batchId)
                ->orderByDesc('id')
                ->paginate(50)
                ->withQueryString();

            $stats = [
                'queued' => MailLog::where('batch_id', $batchId)->where('status', 'queued')->count(),
                'sent' => MailLog::where('batch_id', $batchId)->where('status', 'sent')->count(),
                'failed' => MailLog::where('batch_id', $batchId)->where('status', 'failed')->count(),
            ];
        }

        return view('emails.bulk_mail.create', compact(
            'count',
            'departments',
            'users',
            'logs',
            'batchId',
            'stats'
        ));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'content' => ['required', 'string', 'max:10000'],
            'send_all' => ['nullable', 'boolean'],
            'department_ids' => ['nullable', 'array'],
            'department_ids.*' => ['integer', 'exists:departments,id'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'seconds_per_email' => ['nullable', 'integer', 'min:0', 'max:60'],
            'only_verified' => ['nullable', 'boolean'],
        ]);

        $subject = $data['subject'];
        $contentTemplate = $data['content'];
        $sendAll = (bool)($data['send_all'] ?? false);
        $secondsPerEmail = (int)($data['seconds_per_email'] ?? 0);
        $onlyVerified = (bool)($data['only_verified'] ?? false);
        $selectedDepartmentIds = collect($data['department_ids'] ?? [])->map(fn($id) => (int)$id);
        $selectedUserIds = collect($data['user_ids'] ?? [])->map(fn($id) => (int)$id);

        if (!$sendAll && $selectedDepartmentIds->isEmpty() && $selectedUserIds->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['recipients' => 'Vui lòng chọn ít nhất 1 người nhận (email, phòng ban hoặc tất cả).']);
        }

        $recipientQuery = User::query()
            ->select('id', 'name', 'yourname', 'email', 'department_id')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'like', '%@%');

        if ($onlyVerified && \Schema::hasColumn('users', 'email_verified_at')) {
            $recipientQuery->whereNotNull('email_verified_at');
        }

        if (!$sendAll) {
            $departmentUserIds = collect();

            if ($selectedDepartmentIds->isNotEmpty()) {
                $allDepartmentIds = $selectedDepartmentIds
                    ->flatMap(fn($departmentId) => Department::getChildIds($departmentId))
                    ->unique()
                    ->values();

                $departmentUserIds = User::query()
                    ->whereIn('department_id', $allDepartmentIds)
                    ->pluck('id');
            }

            $finalUserIds = $selectedUserIds
                ->merge($departmentUserIds)
                ->unique()
                ->values();

            if ($finalUserIds->isEmpty()) {
                return back()
                    ->withInput()
                    ->withErrors(['recipients' => 'Không tìm thấy người dùng hợp lệ từ lựa chọn của bạn.']);
            }

            $recipientQuery->whereIn('id', $finalUserIds);
        }

        $users = $recipientQuery->orderBy('id')->get();

        if ($users->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['recipients' => 'Không có email hợp lệ để gửi.']);
        }

        $batchId = now()->format('YmdHis') . '-' . Str::random(6);

        @set_time_limit(0);
        $rows = [];

        foreach ($users as $i => $u) {
            $displayName = $u->yourname ?: ($u->name ?: 'Ban');
            $personalContent = str_replace(
                ['{name}', '{email}'],
                [$displayName, $u->email ?? ''],
                $contentTemplate
            );

            try {
                Mail::to($u->email)->send(
                    new BulkPersonalMail($displayName, $personalContent, $subject)
                );

                $rows[] = [
                    'batch_id' => $batchId,
                    'user_id' => $u->id,
                    'email' => $u->email,
                    'subject' => $subject,
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } catch (Throwable $e) {
                $rows[] = [
                    'batch_id' => $batchId,
                    'user_id' => $u->id,
                    'email' => $u->email,
                    'subject' => $subject,
                    'status' => 'failed',
                    'sent_at' => null,
                    'error' => $e->getMessage(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if ($secondsPerEmail > 0 && $i < ($users->count() - 1)) {
                sleep($secondsPerEmail);
            }
        }

        if (!empty($rows)) {
            MailLog::insert($rows);
        }

        return redirect()
            ->route('admin.bulk_mail.create', ['batch_id' => $batchId])
            ->with('status', "Đã gửi xong {$users->count()} email. Batch: {$batchId}");
    }
}
