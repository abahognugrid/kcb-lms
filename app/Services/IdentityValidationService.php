<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IdentityValidationService
{
  public function verifyNIN(string $nationalIdNumber): array
  {
    if ($nationalIdNumber === '') {
      return [];
    }

      try {
          $response = Http::withHeaders([
              'Authorization' => 'Bearer ' . $this->authenticate(),
          ])->post(str(config('services.gnugrid.url'))->append('v1/validate-nin')->toString(), [
              'nin' => $nationalIdNumber,
          ]);

          if ($response->failed()) {
              throw new Exception($response->body());
          }

          $validationDetails = $response->json('validation', []);

          if (empty($validationDetails)) {
              throw new Exception('Validation failed! It returned empty array');
          }

          return Arr::only($validationDetails, ['timestamp', 'nin', 'date_of_birth', 'name', 'nin_status', 'status']);
      } catch (\Throwable $th) {
        Log::error($th->getMessage());

        return [
            'timestamp' => '1001-01-01 00:37:30',
            'nin' => $nationalIdNumber,
            'date_of_birth' => '1001-01-01',
            'name' => 'Invalid Invalid',
            'nin_status' => 'INVALID',
            'status' => 'FAILED'
        ];
      }

  }

    public function validateId(string $nationalIdNumber, int $documentId, string $dateOfBirth): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->authenticate(),
            ])->post(str(config('services.gnugrid.url'))->append('v1/validate-personal-info')->toString(), [
                'nin' => $nationalIdNumber,
                'document_id' => $documentId,
                'date_of_birth' => $dateOfBirth,
            ]);

            if ($response->failed()) {
                Log::error($response->body());

                return [];
            }

            $validationDetails = $response->json('validation', []);

            if (empty($validationDetails)) {
                Log::error('Validation failed! It returned empty array. Details: ' . $response->body());

                return [];
            }

            return $validationDetails;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return [];
        }
    }

    public function authenticate(): string
    {
        $response = Http::post(str(config('services.gnugrid.url'))->append('v1/oauth/token')->toString(), [
            'grant_type' => 'client_credentials',
            'client_id' => config('services.gnugrid.key'),
            'client_secret' => config('services.gnugrid.secret'),
        ]);

        return Arr::get($response->json(), 'access_token', '');
    }
}
