<?php

namespace App\Http\Controllers\KCB;

use App\Http\Controllers\Controller;
use App\Models\KCB\CustomerRegistrationRequest;
use App\Models\KCB\CustomerRegistrationResponse;
use App\Models\KCB\GetCustomerDetailsRequest;
use App\Models\KCB\GetCustomerDetailsResponse;
use App\Models\KCB\InitiateLoanApplicationRequest;
use App\Models\KCB\InitiateLoanApplicationResponse;
use App\Models\KCB\InitiateLoanRepaymentRequest;
use App\Models\KCB\InitiateLoanRepaymentResponse;
use App\Services\KCB\CustomerDetailsService;
use App\Services\KCB\CustomerRegistrationService;
use App\Services\KCB\LoanApplicationService;
use App\Services\KCB\LoanRepaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KCBUssdChannelApiController extends Controller
{
    protected $registrationService;
    protected $customerDetailsService;
    protected $loanApplicationService;
    protected $loanRepaymentService;

    public function __construct(CustomerRegistrationService $registrationService, CustomerDetailsService $customerDetailsService, LoanApplicationService $loanApplicationService, LoanRepaymentService $loanRepaymentService)
    {
        $this->registrationService = $registrationService;
        $this->customerDetailsService = $customerDetailsService;
        $this->loanApplicationService = $loanApplicationService;
        $this->loanRepaymentService = $loanRepaymentService;
    }

    public function contextCustomerRegistration(Request $request)
    {
        // Log the incoming request
        Log::info('Airtel Customer Registration Request', [
            'headers' => $request->headers->all(),
            'content' => $request->getContent()
        ]);

        try {
            // Get the raw XML content
            $xmlContent = $request->getContent();

            if (empty($xmlContent)) {
                return $this->errorResponse('Empty request body', 400);
            }

            // Parse XML request
            $registrationRequest = CustomerRegistrationRequest::fromXml($xmlContent);

            // Process the registration
            $response = $this->registrationService->registerCustomer($registrationRequest);

            // Return XML response
            return response($response->toXml(), 200)
                ->header('Content-Type', 'application/xml');
        } catch (\Exception $e) {
            Log::error('Customer registration error: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return $this->errorResponse('Processing error: ' . $e->getMessage(), 500);
        }
    }

    private function errorResponse($message, $statusCode)
    {
        $errorResponse = new CustomerRegistrationResponse(
            null,
            null,
            'ERROR',
            $message
        );

        return response($errorResponse->toXml(), $statusCode)
            ->header('Content-Type', 'application/xml');
    }

    public function contextGetCustomerDetails(Request $request)
    {
        // Log the incoming request
        Log::info('Airtel Get Customer Details Request', [
            'headers' => $request->headers->all(),
            'content' => $request->getContent()
        ]);

        try {
            // Get the raw XML content
            $xmlContent = $request->getContent();

            if (empty($xmlContent)) {
                return $this->getCustomerDetailsErrorResponse('Empty request body', 400);
            }

            // Parse XML request
            $detailsRequest = GetCustomerDetailsRequest::fromXml($xmlContent);

            // Validate request type
            $validRequestTypes = ['LOAN_STATUS', 'CREDITLIMIT', 'ACCOUNT_BALANCE'];
            if (!in_array(strtoupper($detailsRequest->requesttype), $validRequestTypes)) {
                return $this->getCustomerDetailsErrorResponse('Invalid request type. Must be: LOAN_STATUS, CREDITLIMIT, or ACCOUNT_BALANCE', 400);
            }

            // Process the request
            $response = $this->customerDetailsService->getCustomerDetails($detailsRequest);

            // Return XML response
            return response($response->toXml(), 200)
                ->header('Content-Type', 'application/xml');
        } catch (\Exception $e) {
            Log::error('Get customer details error: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            $errorResponse = new GetCustomerDetailsResponse(
                null,
                'UNREGISTERED',
                null,
                'ERROR',
                'Processing error: ' . $e->getMessage()
            );

            return response($errorResponse->toXml(), 500)
                ->header('Content-Type', 'application/xml');
        }
    }

    private function getCustomerDetailsErrorResponse($message, $statusCode)
    {
        $errorResponse = new GetCustomerDetailsResponse(
            null,
            'UNREGISTERED',
            null,
            'ERROR',
            $message
        );

        return response($errorResponse->toXml(), $statusCode)
            ->header('Content-Type', 'application/xml');
    }

    public function contextInitiateLoanApplication(Request $request)
    {
        try {
            // Get the raw XML content
            $xmlContent = $request->getContent();

            if (empty($xmlContent)) {
                return $this->initiateLoanApplicationErrorResponse('Empty request body', 400);
            }

            // Parse XML request
            $loanRequest = InitiateLoanApplicationRequest::fromXml($xmlContent);

            // Process the loan application
            $response = $this->loanApplicationService->initiateLoanApplication($loanRequest);

            // Return XML response
            return response($response->toXml(), 200)
                ->header('Content-Type', 'application/xml');
        } catch (\Exception $e) {
            Log::error('Loan application initiation error: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            $errorResponse = new InitiateLoanApplicationResponse(
                null,
                'ERROR',
                'Processing error: ' . $e->getMessage()
            );

            return response($errorResponse->toXml(), 500)
                ->header('Content-Type', 'application/xml');
        }
    }

    private function initiateLoanApplicationErrorResponse($message, $statusCode)
    {
        $errorResponse = new InitiateLoanApplicationResponse(
            null,
            'ERROR',
            $message
        );

        return response($errorResponse->toXml(), $statusCode)
            ->header('Content-Type', 'application/xml');
    }

    public function contextInitiateLoanRepayment(Request $request)
    {
        // Log the incoming request
        Log::info('Airtel Initiate Loan Repayment Request', [
            'headers' => $request->headers->all(),
            'content' => $request->getContent()
        ]);

        try {
            // Get the raw XML content
            $xmlContent = $request->getContent();

            if (empty($xmlContent)) {
                return $this->initiateLoanRepaymentErrorResponse('Empty request body', 400);
            }

            // Parse XML request
            $repaymentRequest = InitiateLoanRepaymentRequest::fromXml($xmlContent);

            // Process the loan repayment
            $response = $this->loanRepaymentService->initiateLoanRepayment($repaymentRequest);

            // Return XML response
            return response($response->toXml(), 200)
                ->header('Content-Type', 'application/xml');
        } catch (\Exception $e) {
            Log::error('Loan repayment initiation error: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            $errorResponse = new InitiateLoanRepaymentResponse(
                null,
                null,
                null,
                null,
                'ERROR',
                'Processing error: ' . $e->getMessage()
            );

            return response($errorResponse->toXml(), 500)
                ->header('Content-Type', 'application/xml');
        }
    }

    private function initiateLoanRepaymentErrorResponse($message, $statusCode)
    {
        $errorResponse = new InitiateLoanRepaymentResponse(
            null,
            null,
            null,
            null,
            'ERROR',
            $message
        );

        return response($errorResponse->toXml(), $statusCode)
            ->header('Content-Type', 'application/xml');
    }
}
