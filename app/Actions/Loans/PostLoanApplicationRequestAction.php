<?php

namespace App\Actions\Loans;

use App\Models\LoanApplication;
use App\Models\PartnerApiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PostLoanApplicationRequestAction
{
    /**
     * @throws \Exception
     */
    public function execute(array $payload)
    {
        $response = $this->sendRequest($payload);

        if ($response->getStatusCode() >= 400) {
            Log::debug($response->getContent());

            throw new \Exception($response->getContent());
        }

        $details = json_decode($response->getContent(), true);

        if ($errorMessage = Arr::get($details, 'error')) {
            Log::error(json_encode($details));

            throw new \Exception($errorMessage);
        }

        if (! empty(Arr::get($details, 'errors'))) {
            Log::debug(json_encode($details));

            throw new \Exception('Error validation payload');
        }

        return LoanApplication::query()->where(['Request_ID' => data_get($details, 'returnData.requestId')])->firstOrFail();
    }

    /**
     * @throws \Exception
     */
    protected function sendRequest(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        $apiSettings = PartnerApiSetting::query()
            ->with('partner')
            ->where('partner_id', Arr::get($payload, 'partnerId'))
            ->first();

        $request = Request::create('/api/ussd/postLoanApplication', 'POST', $payload, [], [], [], json_encode($payload));
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('X-PARTNER-CODE', $apiSettings->partner->Identification_Code);
        $request->headers->set('Authorization', 'Bearer ' . $apiSettings->api_key);

        return app()->handle($request);
    }
}
