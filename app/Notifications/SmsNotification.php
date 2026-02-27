<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected \Illuminate\Support\Carbon $queuedAt;

    public function __construct(
        public string $message,
        public string $phoneNumber,
        public int $customerID,
        public ?int $partnerID,
        public ?float $price,
        public ?float $cost,
    ) {
        $this->queuedAt = now(); // Store the timestamp when notification is created
    }

    public function via(object $notifiable): array
    {
        return ['sms', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        // Check if notification is old when actually being processed
        if ($this->isOld()) {
            // Return data indicating this notification was skipped due to age
            return [
                'message' => 'SMS notification skipped - too old (>24 hours)',
                'phoneNumber' => $this->phoneNumber,
                'partnerID' => $this->partnerID,
                'price' => $this->price,
                'cost' => $this->cost,
                'customerID' => $this->customerID,
                'skipped' => true,
                'reason' => 'notification_too_old',
                'original_queued_at' => $this->queuedAt
            ];
        }

        return [
            'message' => $this->message,
            'phoneNumber' => $this->phoneNumber,
            'partnerID' => $this->partnerID,
            'customerID' => $this->customerID,
            'price' => $this->price,
            'cost' => $this->cost,
        ];
    }

    public function toSms($notifiable): string
    {
        if ($this->isOld()) {
            // Return special marker to indicate this SMS should not be sent
            return '__SKIP_OLD_NOTIFICATION__';
        }

        return $this->message;
    }

    private function isOld(): bool
    {
        /**
         * Check if this SMS notification has been sitting in the queue for more than 24 hours.
         * We use the timestamp from when the notification was created/queued rather than
         * relying on database notifications which may not exist yet.
         *
         * todo: Make the hours configurable
         */
        return $this->queuedAt->diffInHours(now()) >= 24;
    }
}
