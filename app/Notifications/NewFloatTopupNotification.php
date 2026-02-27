<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewFloatTopupNotification extends Notification
{
    use Queueable;
    public $partnerID = null;

    public function __construct() {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'A new float top up has been submitted!',
            'url' => '/float-management',
        ];
    }
}
