<?php

namespace App\Services\Sms\Providers;

use AfricasTalking\SDK\AfricasTalking;
use AfricasTalking\SDK\SMS;
use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AfricasTalkingSmsProvider implements SmsProviderInterface
{
    protected SMS $client;

    public function __construct()
    {
        $this->client = (new AfricasTalking(
            config('services.sms.africastalking.username'),
            config('services.sms.africastalking.password')
        ))->sms();
    }

    public function sendSingle(string $phoneNumber, string $message): array
    {
        try {
            $response = $this->client->send([
                'to' => $this->formatPhoneNumber($phoneNumber),
                'message' => $message,
                'from' => $this->senderId
            ]);

            // dd($response);

            return ['success' => true, 'results' => []];
        } catch (\Exception $e) {
            Log::error('AfricasTalking Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'results' => [], 'error' => $e->getMessage()];
        }
    }

    public function sendBulk(array $phoneNumbers, string $message = ''): array
    {
        try {
            $formattedPhoneNumbers = array_map(function ($phone) {
                return [$this->formatPhoneNumber($phone)];
            }, $phoneNumbers);

            $response = $this->client->send([
                'to' => $formattedPhoneNumbers,
                'message' => $message,
            ]);

            // dd($response);

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

    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
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
