<?php

namespace App\Jobs;

use App\Mail\BulkPersonalMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
use App\Models\MailLog;

class SendPersonalEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1; // debug: để 1 cho dễ nhìn lỗi
    public int $timeout = 60;

    public function __construct(
        public int $userId,
        public string $subject,
        public string $contentTemplate,
        public string $batchId
    ) {}

    public function handle(): void
    {
        $user = User::select('id','name','email')
            ->where('id', $this->userId)
            ->whereNotNull('email')
            ->where('email','!=','')
            ->first();

        if (!$user) return;

        $content = str_replace(['{name}','{email}'], [$user->name ?? '', $user->email ?? ''], $this->contentTemplate);

        try {
            Mail::to($user->email)->send(
                new \App\Mail\BulkPersonalMail($user->name ?? 'Bạn', $content, $this->subject)
            );

            \App\Models\MailLog::where('batch_id', $this->batchId)
                ->where('user_id', $user->id)
                ->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error' => null,
                    'updated_at' => now(),
                ]);
        } catch (Throwable $e) {
            \App\Models\MailLog::where('batch_id', $this->batchId)
                ->where('user_id', $user->id)
                ->update([
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                    'updated_at' => now(),
                ]);

            throw $e;
        }
    }


    public function failed(Throwable $e): void
    {
        Log::error('SendPersonalEmailJob FAILED()', [
            'user_id' => $this->userId,
            'message' => $e->getMessage(),
            'class' => get_class($e),
        ]);
    }
}
