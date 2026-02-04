<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Jobs\SendPersonalEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\MailLog;

class BulkMailController extends Controller
{
    public function create(Request $request)
    {
        $count = User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->count();

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
                'sent'   => MailLog::where('batch_id', $batchId)->where('status', 'sent')->count(),
                'failed' => MailLog::where('batch_id', $batchId)->where('status', 'failed')->count(),
            ];
        }

        return view('emails.bulk_mail.create', compact('count', 'logs', 'batchId', 'stats'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'content' => ['required', 'string', 'max:10000'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'seconds_per_email' => ['nullable', 'integer', 'min:1', 'max:60'],
            'only_verified' => ['nullable', 'boolean'],
        ]);

        $subject = $data['subject'];
        $contentTemplate = $data['content'];
        $limit = (int)($data['limit'] ?? 500);
        $secondsPerEmail = (int)($data['seconds_per_email'] ?? 5);
        $onlyVerified = (bool)($data['only_verified'] ?? false);

        $q = User::query()
            ->select('id','name','email')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'like', '%@%'); // lọc sơ bộ email

        if ($onlyVerified && \Schema::hasColumn('users', 'email_verified_at')) {
            $q->whereNotNull('email_verified_at');
        }

        $users = $q->orderBy('id')->limit($limit)->get();

        // batch id để view xem trạng thái
        $batchId = now()->format('YmdHis') . '-' . Str::random(6);

        // insert log queued trước
        $insert = $users->map(fn($u) => [
            'batch_id' => $batchId,
            'user_id' => $u->id,
            'email' => $u->email,
            'subject' => $subject,
            'status' => 'queued',
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        if (!empty($insert)) {
            MailLog::insert($insert);
        }

        $startAt = now()->addSeconds(10);

        foreach ($users as $i => $u) {
            SendPersonalEmailJob::dispatch(
                userId: $u->id,
                subject: $subject,
                contentTemplate: $contentTemplate,
                batchId: $batchId // ✅ thêm batchId
            )
            ->onQueue('mail')
            ->delay($startAt->copy()->addSeconds($i * $secondsPerEmail));
        }

        return redirect()
            ->route('admin.bulk_mail.create', ['batch_id' => $batchId])
            ->with('status', "Đã xếp hàng gửi {$users->count()} email. Batch: {$batchId}");
    }
}
