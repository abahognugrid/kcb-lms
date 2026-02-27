<?php

namespace App\Services\Factories;

use App\Services\Contracts\ProvidesTransactableAPIs;
use App\Services\AirtelOpenApiService;
use App\Exceptions\PaymentServiceException;

class PaymentServiceFactory
{
    public static function create(string $provider, string $environment, array $config): ProvidesTransactableAPIs
    {
        try {
            return match ($provider) {
                'airtel' => new AirtelOpenApiService($environment, $config),
                default => throw new PaymentServiceException('Unknown payment gateway')
            };
        } catch (\Exception $e) {
            throw new PaymentServiceException(
                "Failed to create payment service: {$e->getMessage()}"
            );
        }
    }
}
