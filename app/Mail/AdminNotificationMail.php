<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $notificationTitle,
        public string $notificationMessage,
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject($this->notificationTitle)
            ->view('admin.mail.notification');
    }
}
