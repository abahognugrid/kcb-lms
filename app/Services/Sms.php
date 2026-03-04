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
        try {
            $response = Http::get(config('sms.kcb.base_url') . '/api/kcb/sendsms', [
                'action'      => 'sendmessage',
                'username'    => config('sms.kcb.username'),
                'password'    => config('sms.kcb.password'),
                'recipient'   => $phone,
                'messagetype' => 'SMS:TEXT',
                'messagedata' => $message,
            ]);

            if ($response->successful()) {

                Log::info('SMS sent successfully', [
                    'phone' => $phone,
                    'status' => $response->status(),
                ]);

                return true;
            }

            Log::warning('SMS API returned error', [
                'phone' => $phone,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {

            Log::error('SMS sending failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
