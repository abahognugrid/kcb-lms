<?php

namespace App\Livewire\Dashboard;

use App\Models\Accounts\Account;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\SmsLog;
use App\Notifications\SmsNotification;
use App\Services\Account\AccountSeederService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        // $loan->arrears_rate = $loan->schedule_sum_principal_remaining > 0 ? round($loan->total_principal_arrears / $loan->schedule_sum_principal_remaining * 100, 2) : 0;
        $loanSummary = DB::table('loans as l')
            ->selectRaw('
        sum(ls.interest_remaining) + sum(ls.principal_remaining) as total_outstanding,
        sum(ls.principal) - sum(ls.principal_remaining) as principal_paid,
        sum(ls.principal_remaining) as outstanding_principal,
        sum(ls.interest_remaining) as outstanding_interest,
        sum(ls.interest) - sum(ls.interest_remaining) as interest_paid,
        COALESCE(sum(lp."Amount_To_Pay"), 0) as penalties_total,
        COALESCE(sum(lp."Amount"), 0) as penalties_paid,
        sum(ls.total_outstanding) + (COALESCE(sum(lp."Amount_To_Pay"), 0) - COALESCE(sum(lp."Amount"), 0)) as outstanding,
        SUM(CASE
            WHEN l."Maturity_Date" < ? THEN principal_remaining
            ELSE 0
        END) as defaulted_loan_amount
    ', [
                now()->toDateString()
            ])
            ->join('loan_schedules as ls', 'l.id', '=', 'ls.loan_id')
            ->leftJoin('loan_penalties as lp', 'l.id', '=', 'lp.Loan_ID')
            ->whereNotIn('l.Credit_Account_Status', [
                Loan::ACCOUNT_STATUS_WRITTEN_OFF,
                Loan::ACCOUNT_STATUS_FULLY_PAID_OFF,
            ])
            ->where('l.partner_id', auth()->user()->partner_id)
            ->first();

        $fullyPaidLoans = Loan::query()->select('Credit_Account_Status')
            ->where('Credit_Account_Status', Loan::ACCOUNT_STATUS_FULLY_PAID_OFF)
            ->toBase()
            ->count();

        $rejectedLoans = LoanApplication::query()
            ->select('Credit_Application_Status')
            ->where('Credit_Application_Status', 'Rejected')
            ->count();

        $total_float_balance = 0;

        $disbursement_ova = Account::where('partner_id', auth()->user()->partner_id)
            ->where('slug', AccountSeederService::DISBURSEMENT_OVA_SLUG)
            ->first();

        if ($disbursement_ova) {
            $total_float_balance = $disbursement_ova->current_balance;
        }

        $totalIncome = Account::query()->where('partner_id', auth()->user()->partner_id)
            ->where('type_letter', '=', 'I')
            ->sum('balance');

        $arrears = $this->getArrearsStatistics();
        $defaultLoans = $arrears->total_arrears_amount;

        $smsNotificationsCount = DB::table('notifications')
            ->where('type', SmsNotification::class)->count();

        $bulkSmsCount = SmsLog::where('Status', 'Sent')->count();
        $bulkSmsCost = $bulkSmsCount * 25;

        $disbursedLoansCount = Loan::query()->select('id')->has('disbursement')->count();
        $defaultRate = ($disbursedLoansCount === 0 ? 0 : round($arrears->loans_in_arrears_count / $disbursedLoansCount * 100, 2)) . '%';

        $repaymentRate = $this->getRepaymentRate();

        return view('livewire.dashboard.statistics', [
            'totalFloat' => number_format($total_float_balance),
            'principalOutstanding' => $loanSummary->outstanding_principal,
            'interestOutstanding' => number_format($loanSummary->outstanding_interest),
            'outstanding' => number_format($loanSummary->total_outstanding),
            'penaltyOutstanding' => number_format($loanSummary->penalties_total - $loanSummary->penalties_paid),
            'fullyPaidLoans' => number_format($fullyPaidLoans),
            'rejectedLoans' => number_format($rejectedLoans),
            'totalIncome' => number_format($totalIncome),
            'defaultLoans' => $defaultLoans,
            'smsNotificationsCount' => $smsNotificationsCount,
            'bulkSmsCount' => $bulkSmsCount,
            'bulkSmsCost' => $bulkSmsCost,
            'defaultRate' => $defaultRate,
            'repaymentRate' => round($repaymentRate, 2),
        ]);
    }
    public function getArrearsStatistics()
    {
        return Loan::query()
            ->whereNotIn('Credit_Account_Status', [
                Loan::ACCOUNT_STATUS_WRITTEN_OFF,
                Loan::ACCOUNT_STATUS_FULLY_PAID_OFF
            ])
            ->whereRelation('schedule', function ($query) {
                $query->where('principal_remaining', '>', 0)
                    ->where('payment_due_date', '<', now()->toDateString());
            })
            ->selectRaw('
                COUNT(DISTINCT loans.id) as loans_in_arrears_count,
                SUM(
                    COALESCE((SELECT SUM(principal_remaining)
                    FROM loan_schedules
                    WHERE loan_schedules.loan_id = loans.id
                    AND loan_schedules.principal_remaining > 0), 0)
                ) as total_arrears_amount')
            ->first();
    }

    private function getRepaymentRate(): float
    {
        $closing_date = now()->toDateString();
        $partner_id = Auth::user()->partner_id;

        $partner_id_condition_lp1 = is_null($partner_id) ? '' : " AND lp1.\"partner_id\" = $partner_id";
        $partner_id_condition_l2 = is_null($partner_id) ? '' : " AND l2.\"partner_id\" = $partner_id";
        $partner_id_condition_l3 = is_null($partner_id) ? '' : " AND l3.\"partner_id\" = $partner_id";
        $partner_id_condition_l4 = is_null($partner_id) ? '' : " AND l4.\"partner_id\" = $partner_id";
        $partner_id_condition_loans = is_null($partner_id) ? '' : " AND loans.\"partner_id\" = $partner_id";

        $query =
            "SELECT
            COALESCE(SUM(principal_paid_less_prepayments),0) AS principal_paid,
            COALESCE(SUM(principal_arrears),0) AS principal_arrears,
            COALESCE(SUM(principal_due_on_reporting_date),0) AS principal_due,
            CASE
                WHEN (COALESCE(SUM(principal_arrears),0) + COALESCE(SUM(principal_due_on_reporting_date),0)) = 0 THEN 0
                ELSE COALESCE(SUM(principal_paid_less_prepayments),0) / (COALESCE(SUM(principal_arrears),0) + COALESCE(SUM(principal_due_on_reporting_date),0))
            END * 100 AS repayment_rate
        FROM (
            SELECT
                loans.id,
                COALESCE(paid_in_period.principal_paid,0) AS paid_in_period,
                principal_due_in_period.amount AS principal_due_in_period,
                principal_arrears.amount AS principal_arrears,
                COALESCE(principal_due_on_reporting_date.amount, 0) AS principal_due_on_reporting_date,
                LEAST(COALESCE(paid_in_period.principal_paid,0), COALESCE(principal_due_in_period.amount, 0)) AS principal_paid_less_prepayments
            FROM loans
            LEFT JOIN (
                SELECT lp1.\"Loan_ID\", SUM(je1.amount) AS principal_paid
                FROM loan_repayments AS lp1
                INNER JOIN journal_entries je1 ON je1.transactable_id = lp1.id
                    AND je1.transactable = 'App\\\\Models\\\\LoanRepayment'
                INNER JOIN accounts acc ON acc.id = je1.account_id
                    AND acc.accountable_type = 'App\\\\Models\\\\LoanProduct'
                WHERE 1=1
                    {$partner_id_condition_lp1}
                    AND lp1.\"Transaction_Date\" <= '{$closing_date}'
                GROUP BY lp1.\"Loan_ID\"
            ) paid_in_period ON paid_in_period.\"Loan_ID\" = loans.id
            INNER JOIN (
                SELECT ls2.loan_id, SUM(ls2.\"principal\") AS amount
                FROM loans AS l2
                INNER JOIN loan_schedules ls2 ON ls2.loan_id = l2.id
                WHERE 1=1
                    {$partner_id_condition_l2}
                    AND ls2.\"payment_due_date\" < '{$closing_date}'
                GROUP BY ls2.loan_id
            ) AS principal_arrears ON principal_arrears.loan_id = loans.id
            INNER JOIN (
                SELECT
                    ls3.loan_id, SUM(ls3.\"principal\") AS amount
                FROM loans AS l3
                INNER JOIN loan_schedules ls3 ON ls3.loan_id = l3.id
                WHERE 1=1
                    {$partner_id_condition_l3}
                    AND ls3.\"payment_due_date\" <= '{$closing_date}'
                GROUP BY ls3.loan_id
            ) AS principal_due_in_period ON principal_due_in_period.loan_id = loans.id
            LEFT JOIN (
                SELECT
                    ls4.loan_id, SUM(ls4.\"principal\") AS amount
                FROM loans AS l4
                INNER JOIN loan_schedules AS ls4 ON ls4.loan_id = l4.id
                WHERE 1=1
                    {$partner_id_condition_l4}
                    AND ls4.\"payment_due_date\" = '{$closing_date}'
                GROUP BY ls4.loan_id
            ) AS principal_due_on_reporting_date ON principal_due_on_reporting_date.loan_id = loans.id
            WHERE 1=1
                {$partner_id_condition_loans}
        ) AS al";

        return DB::select($query)[0]->repayment_rate;
    }
}
