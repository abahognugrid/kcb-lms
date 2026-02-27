<?php

namespace App\Http\Controllers;

class FinancialReportsController extends Controller
{
    public function trialBalance()
    {
        return view('reports.financial.trial-balance');
    }

    public function balanceSheet()
    {
        return view('reports.financial.balance-sheet');
    }

    public function incomeStatement()
    {
        return view('reports.financial.income-statement');
    }

    /**
     * Display a summary of the general ledger.
     *
     * This method retrieves and calculates the total debit, total credit,
     * and balance for each account based on the journal entries. The balance
     * is calculated based on the account type: assets show debits minus credits,
     * while liabilities and capital show credits minus debits. The results are
     * then passed to the 'general-ledger-summary' view for display.
     *
     * For asset accounts, the balance is calculated as debits - credits.
     * For liability and equity accounts, the balance is calculated as credits - debits.
     *
     * @return \Illuminate\View\View
     */
    public function generalLedgerSummary()
    {
        return view('reports.financial.general-ledger-summary');
    }

    public function generalLedgerBreakDown()
    {
        return view('reports.financial.statement-break-down');
    }

    public function cashFlowStatement()
    {
        return view('reports.financial.cash-flow-statement');
    }

    public function incomeReport()
    {
        return view('reports.financial.income-report');
    }
}
