<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Jobs\SendPersonalEmailJob;
use App\Models\Department;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Http\Request;
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
        $sendAll = (bool) ($data['send_all'] ?? false);
        $onlyVerified = (bool) ($data['only_verified'] ?? false);
        $manualDelaySeconds = (int) ($data['seconds_per_email'] ?? 0);
        $selectedDepartmentIds = collect($data['department_ids'] ?? [])->map(fn ($id) => (int) $id);
        $selectedUserIds = collect($data['user_ids'] ?? [])->map(fn ($id) => (int) $id);

        if (! $sendAll && $selectedDepartmentIds->isEmpty() && $selectedUserIds->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['recipients' => 'Vui long chon it nhat 1 nguoi nhan (email, phong ban hoac tat ca).']);
        }

        $recipientQuery = User::query()
            ->select('id', 'name', 'yourname', 'email', 'department_id')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'like', '%@%');

        if ($onlyVerified && \Schema::hasColumn('users', 'email_verified_at')) {
            $recipientQuery->whereNotNull('email_verified_at');
        }

        if (! $sendAll) {
            $departmentUserIds = collect();

            if ($selectedDepartmentIds->isNotEmpty()) {
                $allDepartmentIds = $selectedDepartmentIds
                    ->flatMap(fn ($departmentId) => Department::getChildIds($departmentId))
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
                    ->withErrors(['recipients' => 'Khong tim thay nguoi dung hop le tu lua chon cua ban.']);
            }

            $recipientQuery->whereIn('id', $finalUserIds);
        }

        $users = $recipientQuery->orderBy('id')->get()
            ->filter(fn ($u) => ! empty($u->email))
            ->unique(fn ($u) => mb_strtolower(trim((string) $u->email)))
            ->values();

        if ($users->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['recipients' => 'Khong co email hop le de gui.']);
        }

        $maxPerRun = max(1, (int) env('BULK_MAIL_MAX_PER_RUN', 120));
        $baseDelay = max($manualDelaySeconds, (int) env('BULK_MAIL_MIN_DELAY_SECONDS', 2));
        $jitterMax = max(0, (int) env('BULK_MAIL_JITTER_SECONDS', 2));
        $chunkSize = max(1, (int) env('BULK_MAIL_CHUNK_SIZE', 20));
        $chunkCooldown = max(0, (int) env('BULK_MAIL_CHUNK_COOLDOWN_SECONDS', 45));

        $totalRecipients = $users->count();
        $truncated = false;
        if ($totalRecipients > $maxPerRun) {
            $users = $users->take($maxPerRun)->values();
            $truncated = true;
        }

        $batchId = now()->format('YmdHis') . '-' . Str::random(6);
        $rows = [];
        $failedQueueingCount = 0;
        $activeTotal = $users->count();
        $nextDelaySeconds = 0;
        $now = now();

        foreach ($users as $i => $u) {
            $status = 'queued';
            $error = null;

            try {
                $pendingDispatch = SendPersonalEmailJob::dispatch(
                    (int) $u->id,
                    $subject,
                    $contentTemplate,
                    $batchId
                );

                if ($nextDelaySeconds > 0) {
                    $pendingDispatch->delay($now->copy()->addSeconds($nextDelaySeconds));
                }
            } catch (Throwable $e) {
                $status = 'failed';
                $error = $e->getMessage();
                $failedQueueingCount++;
            }

            $rows[] = [
                'batch_id' => $batchId,
                'user_id' => $u->id,
                'email' => $u->email,
                'subject' => $subject,
                'status' => $status,
                'sent_at' => null,
                'error' => $error,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($i < ($activeTotal - 1)) {
                $seconds = $baseDelay + ($jitterMax > 0 ? random_int(0, $jitterMax) : 0);
                $nextDelaySeconds += max(0, $seconds);

                if ((($i + 1) % $chunkSize) === 0 && $chunkCooldown > 0) {
                    $nextDelaySeconds += $chunkCooldown;
                }
            }
        }

        if (! empty($rows)) {
            MailLog::insert($rows);
        }

        $queuedCount = max(0, $activeTotal - $failedQueueingCount);
        $message = "Da xep hang {$queuedCount}/{$activeTotal} email. Batch: {$batchId}";
        if ($truncated) {
            $message .= " Gioi han an toan moi lan gui: {$maxPerRun}.";
        }
        if ($failedQueueingCount > 0) {
            $message .= " Co {$failedQueueingCount} email khong xep hang duoc, vui long xem log.";
        }
        $message .= ' Can chay queue worker de gui nen (php artisan queue:work).';

        return redirect()
            ->route('admin.bulk_mail.create', ['batch_id' => $batchId])
            ->with('status', $message);
    }
}
