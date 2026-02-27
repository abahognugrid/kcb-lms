<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function markAsRead($notificationId)
  {
    $notification = auth()->user()->notifications()->findOrFail($notificationId);
    $notification->markAsRead();

    if (Arr::has($notification->data, 'url')) {
      // Redirect to the URL associated with the notification
      return redirect($notification->data['url']);
    }

    if (Arr::has($notification->data, 'filename')) {
      return redirect()->to(route('downloads.index'));
    }

    return redirect()->back();
  }

  public function index()
  {
    $user = Auth::user();
    $partnerId = $user->partner_id;

    $loanQuery = DB::table('loans')->where('partner_id', $partnerId);
    $customersWithLoansCount = $loanQuery->count();
    $customersWithCompletedLoansCount = (clone $loanQuery)->where('Credit_Account_Status', Loan::ACCOUNT_STATUS_FULLY_PAID_OFF)->count();
    $customersWithActiveLoansCount = (clone $loanQuery)->whereNotIn('Credit_Account_Status', [Loan::ACCOUNT_STATUS_WRITTEN_OFF, Loan::ACCOUNT_STATUS_FULLY_PAID_OFF])->count();

    $principalReleasedThisMonth = Loan::query()
      ->has('disbursement')
      ->whereBetween('Credit_Account_Date', [
        Carbon::now()->startOfMonth()->startOfDay()->toDateTimeString(),
        Carbon::now()->endOfMonth()->endOfDay()->toDateTimeString()
      ])
      ->sum(DB::raw('"Facility_Amount_Granted"::NUMERIC'));
    $principalReleasedThisMonth = number_format($principalReleasedThisMonth);
    $principalReleasedThisYear = Loan::query()->has('loan_disbursement')
      ->whereYear('created_at', Carbon::now()->year)
      ->sum(DB::raw('"Facility_Amount_Granted"::NUMERIC'));
    $principalReleasedThisYear = number_format($principalReleasedThisYear);
    $principalReleased = Loan::query()
      ->has('loan_disbursement')
      ->sum(DB::raw('"Facility_Amount_Granted"::NUMERIC'));
    $principalReleased = number_format($principalReleased);

    $transactableFilterCallback = function ($query) {
      $query->where('transactable', 'App\Models\LoanRepayment')
        ->orWhere('transactable', 'App\Models\LoanFee')
        ->orWhere('transactable', 'App\Models\LoanPenalty');
    };
    $collectionsTotal = JournalEntry::query()
      ->where($transactableFilterCallback)
      ->sum('credit_amount');
    $collectionsTotal = number_format($collectionsTotal);

    $collectionsThisMonth = JournalEntry::query()
      ->where($transactableFilterCallback)
      ->whereMonth('created_at', Carbon::now()->month)
      ->whereYear('created_at', Carbon::now()->year)
      ->sum('credit_amount');
    $collectionsThisMonth = number_format($collectionsThisMonth);
    $collectionsThisYear = JournalEntry::query()
      ->where($transactableFilterCallback)
      ->whereYear('created_at', Carbon::now()->year)
      ->sum('credit_amount');
    $collectionsThisYear = number_format($collectionsThisYear);

    $newBorrowersThisYear = Loan::query()
      ->select('customer_id')
      ->whereYear('created_at', Carbon::now()->year)
      ->distinct()
      ->toBase()
      ->count('Customer_ID');

    $newBorrowersThisMonth = Loan::query()
      ->select('customer_id')
      ->whereBetween('created_at', [
        Carbon::now()->startOfMonth()->startOfDay()->toDateTimeString(),
        Carbon::now()->endOfMonth()->endOfDay()->toDateTimeString()
      ])
      ->distinct()
      ->count('Customer_ID');

    $cumulativeBorrowers = Loan::query()
      ->select('customer_id')
      ->distinct()
      ->count('Customer_ID');

    $portfolio_performance_records = $this->getPortfolioPerfomance();

    return view('dashboard.index', compact(
      'customersWithLoansCount',
      'customersWithCompletedLoansCount',
      'customersWithActiveLoansCount',
      'principalReleasedThisMonth',
      'principalReleasedThisYear',
      'principalReleased',
      'collectionsTotal',
      'collectionsThisMonth',
      'collectionsThisYear',
      'newBorrowersThisYear',
      'newBorrowersThisMonth',
      'cumulativeBorrowers',
      'portfolio_performance_records'
    ));
  }

  private function getPortfolioPerfomance()
  {
    $date = Carbon::now()->startOfMonth();
    $ranges = [];

    for ($i = 0; $i <= 2; $i++) {
      $start = $date->copy()->subMonths($i)->startOfMonth();
      $end = ($i === 0) ? now() : $date->copy()->subMonths($i)->endOfMonth();

      $closing_date = $end->toDateString();

      $principal_arrears_at_30 = $this->principalArrearsSubSelect($closing_date, 0, 30);
      $principal_arrears_at_60 = $this->principalArrearsSubSelect($closing_date, 31, 60);
      $principal_arrears_at_90 = $this->principalArrearsSubSelect($closing_date, 61, 90);
      $principal_arrears_at_180 = $this->principalArrearsSubSelect($closing_date, 91, 180);
      $principal_arrears_after_180 = $this->principalArrearsSubSelect($closing_date, 181, 0);

      $written_off_account_status = Loan::ACCOUNT_STATUS_WRITTEN_OFF;
      $paid_off_account_status = Loan::ACCOUNT_STATUS_FULLY_PAID_OFF;

      $partner_id = Auth::user()->partner_id;
      $partner_id_loan_condition = $partner_id ? "AND loans.partner_id = {$partner_id}" : "";

      $query =
        "SELECT
        SUM(principal_outstanding) AS sum_principal_outstanding,
        SUM(principal_arrears) AS sum_principal_arrears,
        SUM(principal_arrears_at_30) AS sum_principal_arrears_at_30,
        SUM(principal_arrears_at_60) AS sum_principal_arrears_at_60,
        SUM(principal_arrears_at_90) AS sum_principal_arrears_at_90,
        SUM(principal_arrears_at_180) AS sum_principal_arrears_at_180,
        SUM(principal_arrears_after_180) AS sum_principal_arrears_after_180
      FROM (
        SELECT
          (
            CAST(loans.\"Credit_Amount\" AS NUMERIC) - COALESCE((
            SELECT SUM(je.amount)
            FROM loan_repayments lp
            INNER JOIN journal_entries je ON je.transactable_id = lp.id AND je.transactable = 'App\\\\Models\\\\LoanRepayment'
            INNER JOIN accounts acc ON acc.id = je.account_id AND acc.accountable_type = 'App\\\\Models\\\\LoanProduct'
            WHERE 1=1
              AND lp.\"Loan_ID\" = loans.id
              AND lp.\"Transaction_Date\" <= '{$closing_date}'
            ),0)
          ) AS principal_outstanding,
          (
            SELECT
              COALESCE(SUM(principal_remaining),0)
            FROM loan_schedules
            WHERE loan_id = loans.id
              AND total_outstanding > 0
              AND \"payment_due_date\" < '{$closing_date}'
              AND loan_schedules.deleted_at IS NULL
          ) AS principal_arrears,
          ($principal_arrears_at_30) AS principal_arrears_at_30,
          ($principal_arrears_at_60) AS principal_arrears_at_60,
          ($principal_arrears_at_90) AS principal_arrears_at_90,
          ($principal_arrears_at_180) AS principal_arrears_at_180,
          ($principal_arrears_after_180) AS principal_arrears_after_180
        FROM loans
        WHERE 1=1
          AND \"Credit_Account_Status\" NOT IN (
            $written_off_account_status,
            $paid_off_account_status
          )
          {$partner_id_loan_condition}
          AND EXISTS (
            SELECT 1 FROM loan_schedules
            WHERE loan_schedules.loan_id = loans.id
            AND principal_remaining > 0
            AND loan_schedules.deleted_at IS NULL
          )
          AND loans.deleted_at IS NULL
      ) al";

      $aggregations = DB::select($query)[0];

      $ranges[] = [
        'start' => $start->toDateString(),
        'end' => $end->toDateString(),
        'month' => $start->format('F'),
        'sum_principal_outstanding' => $aggregations->sum_principal_outstanding,
        'sum_principal_arrears' => $aggregations->sum_principal_arrears,
        'sum_principal_arrears_at_30' => $aggregations->sum_principal_arrears_at_30,
        'sum_principal_arrears_at_60' => $aggregations->sum_principal_arrears_at_60,
        'sum_principal_arrears_at_90' => $aggregations->sum_principal_arrears_at_90,
        'sum_principal_arrears_at_180' => $aggregations->sum_principal_arrears_at_180,
        'sum_principal_arrears_after_180' => $aggregations->sum_principal_arrears_after_180
      ];
    }

    return $ranges;
  }

  private function principalArrearsSubSelect(string $closing_date, int $min_days, int $max_days)
  {
    $closing_date = Carbon::parse($closing_date)->subDays($min_days)->toDateString();
    $starting_date = Carbon::parse($closing_date)->subDays($max_days)->toDateString();
    $starting_date_condition = "";
    $closing_date_condition = "";

    if ($min_days === 0) {
      $starting_date_condition = "AND \"payment_due_date\" >= '{$starting_date}'";
      $closing_date_condition = "AND \"payment_due_date\" < '{$closing_date}'";
    } else if ($max_days === 0) {
      $closing_date_condition = "AND \"payment_due_date\" < '{$closing_date}'";
    } else {
      $starting_date_condition = "AND \"payment_due_date\" >= '{$starting_date}'";
      $closing_date_condition = "AND \"payment_due_date\" < '{$closing_date}'";
    }

    return "SELECT
        COALESCE(SUM(\"principal_remaining\"),0)
      FROM loan_schedules
      WHERE loan_id = loans.id
        AND \"total_outstanding\" > 0
        $starting_date_condition
        $closing_date_condition
        AND loan_schedules.deleted_at IS NULL";
  }
}
