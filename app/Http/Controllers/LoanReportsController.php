<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use Illuminate\View\View;

class LoanReportsController extends Controller
{
    public function disbursement()
    {
        return view('reports.loans.disbursement');
    }

    public function outstanding()
    {
        return view('reports.loans.outstanding');
    }

    public function paidOff(): View
    {
        return view('reports.loans.paid_off');
    }

    public function repaymentReport(): View
    {
        return view('reports.loans.repayment_report');
    }

    public function provisionsReport(): View
    {
        return view('reports.loans.provisions_report');
    }

    public function writtenOffReport(): View
    {
        return view('reports.loans.written_off_report');
    }

    public function writtenOffRecoveredReport(): View
    {
        return view('reports.loans.written_off_recovered_report');
    }

    public function overdue(): View
    {
        return view('reports.loans.overdue');
    }

    public function portfolio_at_risk(): View
    {
        return view('reports.loans.portfolio_at_risk');
    }

    public function arrearsAgingReport()
    {
        return view('reports.loans.loan-ageing-report');
    }

    public function pendingDisbursements()
    {
        return view('reports.loans.pending_disbursement_report');
    }

    public function rejectedApplications()
    {
        $rejected_applications = LoanApplication::with([
            'customer',
            'loan_product',
        ])
            ->where('Credit_Application_Status', 'Rejected')
            ->orderBy('id', 'desc')
            ->paginate(100);

        return view('reports.loans.rejected_applications_report', compact('rejected_applications'));
    }

    public function dueLoansReport(): \Illuminate\Contracts\View\View
    {
        return view('reports.loans.due_loans_report');
    }

    public function accountLedger()
    {
        return view('reports.others.account-ledger');
    }

    public function externalAccountsReport(): View
    {
        return view('reports.others.external-accounts-report');
    }
}
