<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class InternalAccountingExceptionNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the slack representation of the notification.
     *
     * Stack trace is trimmed, otherwise it overflows the ->content()
     * and breaks the markdown formatting.
     *
     * @param  mixed  $package
     * @return SlackMessage
     */
    public function toSlack($notifiable): SlackMessage
    {
        $transaction_details = $notifiable->routes['details']['details'];
        $title = $notifiable->routes['title'];

        return (new SlackMessage())->attachment(function ($attachment) use ($title, $transaction_details) {
            $attachment->title($title)
                ->fields([
                    'Env' => config('app.env'),
                    '' => '',
                    'payload' => json_encode($transaction_details, JSON_PRETTY_PRINT),
                ]);
        });
    }
}
