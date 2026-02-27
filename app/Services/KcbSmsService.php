<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KcbSmsService
{
    public function send(string $phone, string $message): array
    {
        $response = Http::get(config('sms.kcb.base_url') . '/api/kcb/sendsms', [
            'action'      => 'sendmessage',
            'username'    => config('sms.kcb.username'),
            'password'    => config('sms.kcb.password'),
            'recipient'   => $phone,
            'messagetype' => 'SMS:TEXT',
            'messagedata' => $message, // Http client auto-encodes
        ]);

        Log::info('KCB SMS response', [
            'phone' => $phone,
            'response' => $response->body(),
        ]);

        return [
            'status' => $response->successful(),
            'body'   => $response->body(),
        ];
    }
}
