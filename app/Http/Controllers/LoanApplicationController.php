<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientAccountBalanceException;
use Exception;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\LoanProduct;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\LoanApplication;
use App\Models\LoanProductType;
use App\Models\Accounts\Account;
use Illuminate\Support\Facades\Auth;
use App\Events\LoanApplicationRejected;
use Illuminate\Support\Facades\Storage;
use App\Services\Account\AccountSeederService;

class LoanApplicationController extends Controller
{
    public function index(Request $request)
    {
        return view('loan-applications.index');
    }

    public function create()
    {
        return view('loan-applications.create');
    }

    public function generateLoanSummary(Request $request, Customer $customer)
    {
        $request->validate([

            "Loan_Product_ID" => "required|integer",
            "Loan_Purpose" => "required|string",
            "Credit_Application_Date" => "required|date",
            "Amount" => "required|integer",
        ]);

        try {
            $loan_amount = $request->Amount;
            $loan_product_id = $request->Loan_Product_ID;
            $loan_purpose = $request->Loan_Purpose;
            $application_date = $request->Credit_Application_Date;
            $loanProduct = LoanProduct::where('id', $loan_product_id)->first();
            $frequencyOfInstallmentRepayment = "Monthly"; //pick freq dynamically
            $loanSummaryDetails = [];
            $loanData = [];
            $loanRecordDetails = [];

            if (!$customer) {
                throw new Exception('Customer account not found.', 400);
            }

            $loan = $customer->loans()
                ->whereNot('Credit_Account_Status', 4) // Fully Paid
                ->whereNot('Credit_Account_Status', 3) // Written-off
                ->latest()
                ->first();

            if ($loan) {
                throw new Exception('Customer already has an active loan.', 400);
            }

            $loanApplicationSummary = new LoanApplication();

            $loanSummaryDetails = $loanApplicationSummary->generateLoanSummaryDetails($customer, $loan_amount, $loan_purpose, $loan_product_id, $application_date);
            $loanData = $loanSummaryDetails['loanData'];
            $loanRecordDetails = $loanSummaryDetails['loanRecordDetails'];
        } catch (\Throwable $th) {
            return $th;
        }

        return view('loan-applications.summary', compact('customer', 'loanRecordDetails', 'loanData'));
    }

    public function show(LoanApplication $application)
    {
        $customerFiles = Storage::allFiles('customer/loan-applications/' . $application->id);

        return view('loan-applications.show', compact('application', 'customerFiles'));
    }

    public function download(Request $request, LoanApplication $application)
    {
        return Storage::download($request->get('file'));
    }

    public function cancel()
    {
        session()->flash("success", "Loan application cancelled");
        return redirect()->route('loan-applications.index');
    }

    public function reject(Request $request, LoanApplication $loanApplication)
    {
        try {
            $request->validate([
                "Rejection_Reason" => "required|string|min:2|max:255",
                "Rejection_Reference" => "required|string|min:2|max:50",
            ]);

            $loanApplication->update([
                'Credit_Application_Status' => 'Rejected',
                'Rejection_Reason' => $request->Rejection_Reason,
                'Rejection_Reference' => $request->Rejection_Reference,
                'Rejection_Date' => now(),
            ]);

            $loanApplication->setStatus('Rejected');

            event(new LoanApplicationRejected($loanApplication));

            session()->flash("success", "Loan application rejected");
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

        return redirect()->route('loan-applications.index');
    }
}
