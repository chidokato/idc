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
        $onlyVerified = (bool)($data['only_verified'] ?? false);
        $manualDelaySeconds = (int)($data['seconds_per_email'] ?? 0);
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

        $users = $recipientQuery->orderBy('id')->get()
            ->filter(fn($u) => !empty($u->email))
            ->unique(fn($u) => mb_strtolower(trim((string) $u->email)))
            ->values();

        if ($users->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['recipients' => 'Không có email hợp lệ để gửi.']);
        }

        $maxPerRun = max(1, (int) env('BULK_MAIL_MAX_PER_RUN', 120));
        $baseDelay = max($manualDelaySeconds, (int) env('BULK_MAIL_MIN_DELAY_SECONDS', 2));
        $jitterMax = max(0, (int) env('BULK_MAIL_JITTER_SECONDS', 2));
        $chunkSize = max(1, (int) env('BULK_MAIL_CHUNK_SIZE', 20));
        $chunkCooldown = max(0, (int) env('BULK_MAIL_CHUNK_COOLDOWN_SECONDS', 45));
        $stopCheckAfter = max(10, (int) env('BULK_MAIL_FAIL_CHECK_AFTER', 20));
        $maxFailRatePct = max(10, min(90, (int) env('BULK_MAIL_MAX_FAIL_RATE_PERCENT', 35)));

        $totalRecipients = $users->count();
        $truncated = false;
        if ($totalRecipients > $maxPerRun) {
            $users = $users->take($maxPerRun)->values();
            $truncated = true;
        }

        $batchId = now()->format('YmdHis') . '-' . Str::random(6);

        @set_time_limit(0);
        $rows = [];
        $sentCount = 0;
        $failedCount = 0;
        $stoppedBySafety = false;
        $stoppedAtIndex = -1;
        $activeTotal = $users->count();

        foreach ($users as $i => $u) {
            $displayName = $u->yourname ?: ($u->name ?: 'Ban');
            $personalContent = str_replace(
                ['{name}', '{email}'],
                [$displayName, $u->email ?? ''],
                $contentTemplate
            );

            $status = 'sent';
            $error = null;
            $sentAt = null;

            try {
                $this->sendWithSoftRetry($u->email, $displayName, $personalContent, $subject);
                $sentAt = now();
                $sentCount++;
            } catch (Throwable $e) {
                $status = 'failed';
                $error = $e->getMessage();
                $failedCount++;
            }

            $rows[] = [
                'batch_id' => $batchId,
                'user_id' => $u->id,
                'email' => $u->email,
                'subject' => $subject,
                'status' => $status,
                'sent_at' => $sentAt,
                'error' => $error,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $processed = $sentCount + $failedCount;
            if ($processed >= $stopCheckAfter) {
                $failRate = ($failedCount / max(1, $processed)) * 100;
                if ($failRate >= $maxFailRatePct) {
                    $stoppedBySafety = true;
                    $stoppedAtIndex = $i;
                    break;
                }
            }

            if ($i < ($activeTotal - 1)) {
                $seconds = $baseDelay + ($jitterMax > 0 ? random_int(0, $jitterMax) : 0);
                if ($seconds > 0) {
                    sleep($seconds);
                }

                if ((($i + 1) % $chunkSize) === 0 && $chunkCooldown > 0) {
                    sleep($chunkCooldown);
                }
            }
        }

        if ($stoppedBySafety && $stoppedAtIndex >= 0) {
            for ($j = $stoppedAtIndex + 1; $j < $activeTotal; $j++) {
                $u = $users[$j];
                $rows[] = [
                    'batch_id' => $batchId,
                    'user_id' => $u->id,
                    'email' => $u->email,
                    'subject' => $subject,
                    'status' => 'failed',
                    'sent_at' => null,
                    'error' => 'Skipped by safety-stop due to high temporary failure rate.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($rows)) {
            MailLog::insert($rows);
        }

        $message = "Đã gửi xong {$sentCount}/{$activeTotal} email (fail: {$failedCount}). Batch: {$batchId}";
        if ($truncated) {
            $message .= " Giới hạn an toàn mỗi lần gửi: {$maxPerRun}.";
        }
        if ($stoppedBySafety) {
            $message .= " Hệ thống đã tự dừng do tỉ lệ lỗi cao để bảo vệ uy tín gửi mail.";
        }

        return redirect()
            ->route('admin.bulk_mail.create', ['batch_id' => $batchId])
            ->with('status', $message);
    }

    private function sendWithSoftRetry(string $email, string $name, string $content, string $subject): void
    {
        $attempts = 0;
        $maxAttempts = 2;

        while (true) {
            $attempts++;
            try {
                Mail::to($email)->send(
                    new BulkPersonalMail($name, $content, $subject)
                );

                return;
            } catch (Throwable $e) {
                if ($attempts >= $maxAttempts || !$this->isTransientMailError($e)) {
                    throw $e;
                }

                sleep(random_int(3, 7));
            }
        }
    }

    private function isTransientMailError(Throwable $e): bool
    {
        $message = mb_strtolower($e->getMessage());
        $needles = [
            'timed out',
            'timeout',
            'connection could not be established',
            'connection reset',
            'try again later',
            'too many login attempts',
            'rate limit',
            '421',
            '450',
            '451',
            '452',
            '4.7.0',
        ];

        foreach ($needles as $needle) {
            if (str_contains($message, $needle)) {
                return true;
            }
        }

        return false;
    }
}
