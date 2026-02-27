<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanFee;

class FeesReportController extends Controller
{
    public function index()
    {
        // Retrieve all loans with their associated customers and fees
        $loans = Loan::with(['customer', 'fees'])->get();

        // Summary data
        $totalFeesDue = 0;
        $totalFeesPaid = 0;

        // Detailed data
        $reportData = [];

        foreach ($loans as $loan) {
            $totalFeesDueForLoan = $loan->fees->sum('Value');
            $totalFeesPaidForLoan = LoanFee::where('Loan_ID', $loan->id)
                ->sum('Amount');

            $totalFeesDue += $totalFeesDueForLoan;
            $totalFeesPaid += $totalFeesPaidForLoan;
            $total_fees_pending  = $totalFeesDueForLoan == 0 ? 0 : number_format($totalFeesDueForLoan - $totalFeesPaidForLoan, 2);

            $reportData[] = [
                'customer_name' => "{$loan->customer->First_Name} {$loan->customer->Last_Name}",
                'loan_number' => $loan->id,
                'disbursement_date' => $loan->created_at, // Assuming created_at is disbursement date
                'principal_amount' => $loan->Credit_Amount,
                'total_fees_due' => number_format($totalFeesDueForLoan, 2),
                'total_fees_paid' => number_format($totalFeesPaidForLoan, 2),
                'total_fees_pending' => $total_fees_pending,
            ];
        }

        return view('reports.fees.index', compact('totalFeesDue', 'totalFeesPaid', 'reportData'));
    }
}
