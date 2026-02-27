<?php

declare(strict_types=1);

namespace App\Services;

use App\Notifications\SmsNotification;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Sms
{
    public function send($notifiable, SmsNotification $notification): void
    {
        $message = $notification->toSms($notifiable);

        // Check if this is an old notification that should be skipped
        if ($message === '__SKIP_OLD_NOTIFICATION__') {
            return;
        }

        $this->sendSms($notifiable->routeNotificationForSms(), $message);
    }

    public function sendSms($phone, $message): bool
    {
        $response = Http::get(config('sms.kcb.base_url') . '/api/kcb/sendsms', [
            'action'      => 'sendmessage',
            'username'    => config('sms.kcb.username'),
            'password'    => config('sms.kcb.password'),
            'recipient'   => $phone,
            'messagetype' => 'SMS:TEXT',
            'messagedata' => $message, // Http client auto-encodes
        ]);
        if ($response->successful()) {
            Log::info('Sms sent with KCB SMS service', [
                'phone' => $phone,
                'message' => $message,
                'response' => $response->body(),
            ]);
            return true;
        }

        // Optionally, you can throw an exception or handle errors here
        throw new Exception('Failed to send SMS: ' . $response->body());
    }
}
