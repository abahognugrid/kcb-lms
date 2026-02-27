<?php

namespace App\Services\Sms\Providers;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EgoSmsProvider implements SmsProviderInterface
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private string $senderId;

    public function __construct()
    {
        $this->baseUrl = config('services.sms.ego.url');
        $this->username = config('services.sms.ego.username');
        $this->password = config('services.sms.ego.password');
        $this->senderId = config('services.sms.ego.sender_id');
    }

    public function sendSingle(string $phoneNumber, string $message): array
    {
        try {
            $payload = [
                'method' => 'SendSms',
                'userdata' => [
                    'username' => $this->username,
                    'password' => $this->password
                ],
                'msgdata' => [
                    [
                        'number' => $this->formatPhoneNumber($phoneNumber),
                        'message' => $message,
                        'senderid' => $this->senderId,
                        'priority' => '0'
                    ]
                ]
            ];

            $response = Http::timeout(30)
                ->post($this->baseUrl, $payload);

            if (!$response->successful()) {
                Log::error('EgoSMS API Error', $response->json());
                return ['success' => false, 'results' => [], 'error' => data_get($response->json(), 'Message', 'Unknown error')];
            }

            return ['success' => true, 'results' => $response->json()];
        } catch (\Exception $e) {
            Log::error('EgoSMS Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'results' => [], 'error' => $e->getMessage()];
        }
    }

    public function sendBulk(array $phoneNumbers, string $message): array
    {
        try {

            $msgdata = array_map(function($phoneNumber) use ($message) {
                return [
                    'number' => $this->formatPhoneNumber($phoneNumber),
                    'message' => $message,
                    'senderid' => $this->senderId,
                    'priority' => '0'
                ];
            }, $phoneNumbers);

            $payload = [
                'method' => 'SendSms',
                'userdata' => [
                    'username' => $this->username,
                    'password' => $this->password
                ],
                'msgdata' => $msgdata
            ];

            $response = Http::timeout(60)->post($this->baseUrl, $payload);

            if (!$response->successful()) {
                Log::error('EgoSMS Bulk API Error', ['response' => $response->json()]);

                return [
                    'success' => false,
                    'results' => [],
                    'error' => data_get($response->json(), 'Message', 'Unknown error')
                ];
            }

            return [
                'success' => true,
                'results' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('EgoSMS Bulk Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'results' => [], 'error' => $e->getMessage()];
        }
    }

    public function getBalance(): float
    {
        try {
            $payload = [
                'method' => 'GetBalance',
                'userdata' => [
                    'username' => $this->username,
                    'password' => $this->password
                ]
            ];

            $response = Http::post($this->baseUrl, $payload);
            return $response->json()['balance'] ?? 0;
        } catch (\Exception $e) {
            Log::error('EgoSMS Balance Check Error', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    public function getDeliveryStatus(string $messageId): string
    {
        try {
            $payload = [
                'method' => 'GetMessageStatus',
                'userdata' => [
                    'username' => $this->username,
                    'password' => $this->password
                ],
                'msgdata' => [
                    'message_id' => $messageId
                ]
            ];

            $response = Http::post($this->baseUrl, $payload);
            return $response->json()['status'] ?? 'UNKNOWN';
        } catch (\Exception $e) {
            Log::error('EgoSMS Status Check Error', ['error' => $e->getMessage()]);
            return 'ERROR';
        }
    }

    private function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If number starts with 0, replace with 256
        if (str_starts_with($phone, '0')) {
            $phone = '256' . substr($phone, 1);
        }

        // If number doesn't start with 256, add it
        if (!str_starts_with($phone, '256')) {
            $phone = '256' . $phone;
        }

        return $phone;
    }
}
