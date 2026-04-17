<?php

namespace App\Services\KCB;

use App\Models\CreditLimit;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\Partner;
use App\Notifications\SmsNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Cache;

class CreditLimitService
{
    public function __construct(
        protected $customer,
    ) {}

    public function execute(): void
    {
        $lastMaturedLoan = $this->customer->loans
            ->where('Maturity_Date', '<', Carbon::now())
            ->last();

        try {
            $postData = $this->buildPayload($lastMaturedLoan);
            Log::info("CRB Credit Limit Request:\n" . json_encode($postData));

            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post(config('lms.crb.url') . '/v1/credit-enquiries/credit-limits', $postData);

            Log::info("CRB Credit Limits Response:\n" . $response->body());

            if (! $response->successful()) {
                Log::error('API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return;
            }

            $responseData = $response->json();

            if (isset($responseData['error'])) {
                throw new Exception('Credit API error: ' . $responseData['error']);
            }

            $decision = data_get($responseData, 'data.Decision');

            if (! $decision) {
                throw new Exception('Decision block missing in API response');
            }

            $this->storeCreditLimit($decision);
        } catch (Exception $e) {
            Log::error('CreditLimitService error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function buildPayload($lastMaturedLoan): array
    {
        return [
            'phone_number' => $this->customer->Telephone_Number,
            'entity_type' => 1,
            'identification_type' => 'ii_country_id',
            'identifier' => $this->customer->ID_Number,
            'product_id' => LoanProduct::first()->Code,
            'entity_type_category' => 'AGENT',
            'client_consented' => 'Yes',

            'prevLoanCount' => $this->customer->loans->where('Maturity_Date', '<', Carbon::now())->count(),

            'prevLoanDaysInArrears' => $lastMaturedLoan
                ? max(
                    0,
                    $lastMaturedLoan->Maturity_Date
                        ->diffInDays(
                            $lastMaturedLoan->Credit_Account_Closure_Date ?? Carbon::today(),
                            false
                        )
                )
                : null,

            'prevLoansPaidOnTime' => $this->customer->loans
                ->where('Credit_Account_Status', Loan::ACCOUNT_STATUS_FULLY_PAID_OFF)
                ->where('Maturity_Date', '>=', 'Credit_Account_Closure_Date')
                ->count(),

            'prevLoanRepaymentDate' => $lastMaturedLoan?->Credit_Account_Status == Loan::ACCOUNT_STATUS_FULLY_PAID_OFF
                ? $lastMaturedLoan?->Credit_Account_Closure_Date?->toDateString()
                : null,

            'prevLoanDisbursementDate' => $lastMaturedLoan?->Credit_Account_Date?->toDateString(),
            'prevLoanMaturityDate' => $lastMaturedLoan?->Maturity_Date?->toDateString(),
            'prevLoanLimit' => $lastMaturedLoan?->Credit_Limit,
            'prevLoanRepaymentMultiplier' => $lastMaturedLoan?->Repayment_Multiplier,
            'prevLoanDaysLateMultiplier' => $lastMaturedLoan?->Days_Late_Multiplier,
        ];
    }

    protected function storeCreditLimit(array $decision): void
    {
        $creditLimit = data_get($decision, 'credit_limit');
        $isExcluded = data_get($decision, 'evaluation.is_excluded', false);
        $exclusions = data_get($decision, 'evaluation.exclusions', []);
        $repaymentMultiplier = data_get($decision, 'evaluation.repayment_multiplier');
        $daysLateMultiplier = data_get($decision, 'evaluation.previous_loans_days_late_multiplier');

        $partner = Partner::first();

        $creditLimitRecord = CreditLimit::where('customer_id', $this->customer->id)->first();

        $previousCreditLimit = $creditLimitRecord?->credit_limit;

        CreditLimit::updateOrCreate(
            [
                'customer_id' => $this->customer->id,
                'partner_id' => $partner->id,
            ],
            [
                'credit_limit' => $creditLimit,
                'used_credit' => 0,
                'available_credit' => $creditLimit,
                'previous_credit_limit' => $previousCreditLimit,
                'loan_days_late_multiplier' => $daysLateMultiplier,
                'loan_repayment_multiplier' => $repaymentMultiplier,
                'is_excluded' => $isExcluded,
                'exclusions' => $exclusions,
                'data' => $decision,
            ]
        );
    }

    protected function getAccessToken(): string
    {
        $accessToken = Cache::get('api_access_token');
        if ($accessToken) {
            return $accessToken;
        } else {
            // Step 1: Get the access token
            $tokenResponse = Http::asForm()->post(config('lms.crb.url') . '/v1/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('lms.crb.client-id'),
                'client_secret' => config('lms.crb.client-secret'),
            ]);
            if ($tokenResponse->successful()) {
                $accessToken = $tokenResponse->json()['access_token'];
                $tokenLifeTime = $tokenResponse->json()['expires_in']; // seconds
                Cache::put('api_access_token', $accessToken, $tokenLifeTime);
                return $accessToken;
            } else {
                Log::error('Failed to retrieve access token: ' . $tokenResponse->body());
                throw new Exception('Failed to get access token');
            }
        }
    }
}
