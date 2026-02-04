<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkPersonalMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $name;
    public string $content;
    public string $subjectLine;

    public function __construct(string $name, string $content, string $subjectLine)
    {
        $this->name = $name;
        $this->content = $content;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->markdown('emails.bulk.personal', [
                'name' => $this->name,
                'content' => $this->content,
            ]);
    }
}
