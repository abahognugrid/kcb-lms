<?php

declare(strict_types=1);

namespace App\Services;

use App\Notifications\SmsNotification;
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
        if (app()->isLocal()) {
            $response = [
                'status'       => 'Success',
                'recipient'    => $phone,
                'message_id'   => rand(100000, 999999),
                'messagetype'  => 'SMS:TEXT',
                'messagedata'  => $message,
                'timestamp'    => now()->toDateTimeString(),
                'description'  => "Message successfully queued for delivery",
            ];
            Log::info("SMS API Response (Local):\n" . json_encode($response, JSON_PRETTY_PRINT));
            return true;
        }
        $phone = '256700460055'; // Hardcoded for testing, replace with $phone in production
        $response = Http::get(config('sms.kcb.base_url') . '/api/kcb/sendsms', [
            'action'      => 'sendmessage',
            'username'    => config('sms.kcb.username'),
            'password'    => config('sms.kcb.password'),
            'recipient'   => $phone, // change this to $phone
            'messagetype' => 'SMS:TEXT',
            'messagedata' => $message,
        ]);
        // Log the response body
        Log::info("SMS API Response:\n" . $response->body());
        if ($response->successful()) {
            return true;
        }
        return false;
    }
}
