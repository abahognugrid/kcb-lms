<?php

namespace App\Http\Controllers;

use App\Actions\Loans\WriteOffLoanAction;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\LoanApplication;
use App\Models\LoanDisbursement;
use App\Models\LoanSchedule;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index()
    {
        return view('loans.index');
    }

    public function writeOff(Request $request, WriteOffLoanAction $action, Loan $loan): \Illuminate\Http\RedirectResponse
    {
        $details = $request->validate([
            'write_off_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        try {
            return redirect()
                ->route('loan-accounts.show', ['loan' => $action->execute($loan, $details)])
                ->with('success', 'Loan written off successfully.');
        } catch (\Throwable $th) {
            return back()->withError($th->getMessage());
        }
    }

    public function store(Request $request, LoanApplication $loanApplication)
    {
        return back();
        //        $loanApplication->load('loan_product');
        //
        //        try {
        //            $loan = new Loan();
        //            $loan->fill([
        //                'partner_id' => $loanApplication->partner_id,
        //                'Customer_ID' => $loanApplication->Customer_ID,
        //                'Loan_Product_ID' => $loanApplication->Loan_Product_ID,
        //                'Loan_Application_ID' => $loanApplication->id,
        //                'Credit_Account_Reference' => Loan::generateReference(),
        //                'Credit_Account_Date' => $loanApplication->Credit_Application_Date,
        //                'Credit_Amount' => $loanApplication->Amount,
        //                'Facility_Amount_Granted' => $loanApplication->Amount,
        //                'Credit_Amount_Drawdown' => '0.00',
        //                'Credit_Account_Type' => 'uwow',
        //                'Currency' => 'UGX',
        //                'Maturity_Date' => $loanApplication->Credit_Application_Date,
        //                'Annual_Interest_Rate_at_Disbursement' => 0,
        //                'Date_of_First_Payment' => $loanApplication->Credit_Application_Date,
        //                'Credit_Amortization_Type' => 1, // Refer to the DSM APPENDIX 1.11
        //                'Credit_Payment_Frequency' => "Monthly",
        //                'Number_of_Payments' => 1,
        //                'Term' => 1,
        //                'Type_of_Interest' => 1, // Refer to the DSM APPENDIX 1.9 0-Fixed, 1-Floating
        //                'Interest_Rate' => 2,
        //                'Interest_Calculation_Method' => 'Flat',
        //                'Loan_Term_ID' => 2,
        //            ])->save();
        //            session()->flash("success", "Loan approved successfully");
        //        } catch (\Throwable $th) {
        //            session()->flash("error", $th->getMessage());
        //        }
        //        //return redirect()->route('loan-applications.index');
        //        return redirect()->back();
    }

    public function show(Request $request, Loan $loan)
    {
        $loan->load(['loan_product', 'customer', 'loan_application']);

        $repayments = DB::select(
            "SELECT
        CONCAT('lr-', lr.id) AS id,
        lr.\"Transaction_Date\" AS transaction_date,
        COALESCE(SUM(CASE
            WHEN a.accountable_type = 'App\\\\Models\\\\LoanProduct'
                THEN (COALESCE(je.credit_amount,0) - COALESCE(je.debit_amount,0))
            ELSE 0
        END),0) AS principal_paid,
        COALESCE(SUM(CASE
            WHEN a.slug IN ('interest-income-from-loans')
                THEN (COALESCE(je.credit_amount,0) - COALESCE(je.debit_amount,0))
            ELSE 0
        END),0) AS interest_paid,
        COALESCE(SUM(CASE
            WHEN a.slug IN ('penalties-from-loan-payments')
                THEN (COALESCE(je.credit_amount,0) - COALESCE(je.debit_amount,0))
            ELSE 0
        END),0) AS penalties_paid,
        COALESCE(SUM(CASE
            WHEN a.slug LIKE '%fee%'
                THEN (COALESCE(je.credit_amount,0) - COALESCE(je.debit_amount,0))
            ELSE 0
        END),0) AS fees_paid
    FROM loan_repayments lr
    INNER JOIN journal_entries AS je
        ON je.transactable_id = lr.id AND transactable = 'App\\\\Models\\\\LoanRepayment'
    INNER JOIN accounts a ON a.id = je.account_id
    WHERE 1=1
        AND lr.\"Loan_ID\" = {$loan->id}
    GROUP BY lr.id, lr.\"Transaction_Date\"

    UNION ALL

    SELECT
        CONCAT('wol-', wol.id) AS id,
        wol.\"Written_Off_Date\" AS transaction_date,
        SUM(wol.\"Amount_Written_Off\") AS principal_paid,
        SUM(COALESCE(wol.interest,0)) AS interest_paid,
        SUM(COALESCE(wol.penalties,0)) AS penalties_paid,
        SUM(COALESCE(wol.fees,0)) AS fees_paid
    FROM written_off_loans AS wol
    WHERE 1=1
        AND wol.\"Loan_ID\" = {$loan->id}
        AND wol.\"Is_Recovered\" = true
        AND EXISTS (
            SELECT 1 FROM journal_entries
            WHERE 1=1
                AND transactable = 'App\\\\Models\\\\WrittenOffLoanRecovered'
                AND transactable_id = wol.id
        )
    GROUP BY wol.id, wol.\"Written_Off_Date\"
"
        );
        return view('loans.show', compact('loan', 'repayments'));
    }

    public function ledger(Request $request, Loan $loan)
    {
        $loan->load(['loan_product', 'customer', 'loan_application']);

        return view('loans.ledger', compact('loan'));
    }

    public function paymentVelocity(Request $request, Loan $loan)
    {
        $loan->load(['loan_product', 'customer', 'loan_application']);

        return view('loans.payment-velocity', compact('loan'));
    }

    public function update(Request $request, Loan $loan) {}

    public function disburse(Loan $loan)
    {
        //$this->createTrackingSession($loanApplication);

        LoanDisbursement::createDisbursement($loan);
        LoanSchedule::generateSchedule($loan);

        return true;
    }
}
