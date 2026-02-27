<?php

namespace App\Models;

use App\Enums\CreditPaymentFrequency;
use App\Enums\LoanAccountType;
use App\Models\Accounts\Account;
use App\Models\Scopes\PartnerScope;
use App\Services\Account\AccountSeederService;
use App\Traits\HasStatuses;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;

class Loan extends Model
{
    use HasFactory, HasStatuses, SoftDeletes;

    const APPROVED_STATUS = 'Approved';

    const WRITTEN_OFF_STATUS = 'Written-off';

    const ARREAR_STATUS = 'In-Arrears';

    const ACCOUNT_STATUS_FULLY_PAID_OFF = 4;

    const ACCOUNT_STATUS_WRITTEN_OFF = 3;

    const ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS = 5;

    const ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS = 1;

    const API_STATUS_APPROVED = 'APPROVED';
    const API_STATUS_LIQ = 'LIQ';

    const SUPPORTED_Credit_Account_Statuses = [
        self::ACCOUNT_STATUS_FULLY_PAID_OFF => 'Fully Paid',
        self::ACCOUNT_STATUS_WRITTEN_OFF => 'Written-off',
        self::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS => 'Within Terms',
        self::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS => 'Outstanding and Beyond Terms',
        null => '',
    ];

    protected $fillable = [
        'partner_id',
        'Customer_ID',
        'Loan_Product_ID',
        'Loan_Application_ID',
        'Credit_Application_Status',
        'Credit_Account_Reference',
        'Credit_Account_Date',
        'Credit_Amount',
        'Facility_Amount_Granted',
        'Credit_Amount_Drawdown',
        'Credit_Account_Type',
        'Currency',
        'Maturity_Date',
        'Annual_Interest_Rate_at_Disbursement',
        'Date_of_First_Payment',
        'Credit_Amortization_Type',
        'Credit_Payment_Frequency',
        'Number_of_Payments',
        'Instalment_Amount',
        'Client_Advice_Notice_Flag',
        'Term',
        'Type_of_Interest',
        'Loan_Term_ID',
        'Interest_Rate',
        'Interest_Calculation_Method',
        'Credit_Account_Status',
        'Client_Consent_Flag',
        'Last_Status_Change_Date',
        'Credit_Account_Closure_Date',
        'Credit_Account_Closure_Reason',
        'Credit_Account_Closure_Officer',
        'Written_Off_Date',
        'Written_Off_Amount',
        'Written_Off_Reason',
        'Written_Off_Officer',
        'Written_Off_Amount_Recovered',
        'Last_Recovered_At',
        'Blacklisted_Date',
        'Blacklisted_Reason',
        'Blacklisted_By',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'Last_Status_Change_Date' => 'datetime',
            'Maturity_Date' => 'datetime',
            'Credit_Account_Date' => 'datetime',
            'Date_of_First_Payment' => 'datetime',
            'Credit_Account_Closure_Date' => 'datetime',
            'Written_Off_Date' => 'date',
            'Last_Recovered_At' => 'datetime',
            'Blacklisted_Date' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public function scopeAgeingCategories(Builder $query, string $endDate, Collection $provisions): Builder
    {
        if ($provisions->isEmpty() || $provisions->count() !== 5) {
            return $query->addSelect($this->getDefaultAgeingCategories($endDate));
        }

        return $query->addSelect($this->getCustomAgeingCategories($endDate, $provisions));
    }

    /**
     * Get the default ageing categories.
     */
    private function getDefaultAgeingCategories(string $endDate): array
    {
        return [
            'principal_in_arrears' => $this->buildPrincipalInArrearsQuery($endDate),
            'days_in_arrears' => $this->buildDaysInArrearsQuery($endDate),
            'principal_outstanding_at_30' => $this->buildPrincipalOutstandingQuery($endDate, 30, 0),
            'principal_outstanding_at_60' => $this->buildPrincipalOutstandingQuery($endDate, 60, 31),
            'principal_outstanding_at_90' => $this->buildPrincipalOutstandingQuery($endDate, 90, 61),
            'principal_outstanding_at_180' => $this->buildPrincipalOutstandingQuery($endDate, 180, 91),
            'principal_outstanding_after_180' => $this->buildPrincipalOutstandingQuery($endDate, 0, 181),
        ];
    }

    /**
     * Get custom ageing categories based on provisions.
     */
    private function getCustomAgeingCategories(string $endDate, Collection $provisions): array
    {
        $classifications = [
            'principal_outstanding_at_30',
            'principal_outstanding_at_60',
            'principal_outstanding_at_90',
            'principal_outstanding_at_180',
            'principal_outstanding_after_180',
        ];

        $customizedAgeing = $provisions->mapWithKeys(function ($provision, $key) use ($classifications, $endDate) {
            return [
                $classifications[$key] => $this->buildPrincipalOutstandingQuery(
                    $endDate,
                    $provision->maximum_days,
                    $provision->minimum_days
                ),
            ];
        })->all();

        // Add common queries
        $customizedAgeing['principal_in_arrears'] = $this->buildPrincipalInArrearsQuery($endDate);
        $customizedAgeing['days_in_arrears'] = $this->buildDaysInArrearsQuery($endDate);

        return $customizedAgeing;
    }

    /**
     * Build a query for principal in arrears.
     */
    private function buildPrincipalInArrearsQuery(string $endDate): \Illuminate\Database\Eloquent\Builder
    {
        return LoanSchedule::query()
            ->selectRaw('sum(principal_remaining)')
            ->whereColumn('loan_id', 'loans.id')
            ->where('total_outstanding', '>', 0)
            ->whereDate('payment_due_date', '<', $endDate)
            ->limit(1);
    }

    /**
     * Build a query for days in arrears.
     */
    private function buildDaysInArrearsQuery(string $endDate): \Illuminate\Database\Eloquent\Builder
    {
        return LoanSchedule::query()
            ->selectRaw('datediff(payment_due_date, ?)', [$endDate])
            ->whereColumn('loan_id', 'loans.id')
            ->where('total_outstanding', '>', 0)
            ->whereDate('payment_due_date', '<', $endDate)
            ->orderBy('payment_due_date')
            ->limit(1);
    }

    /**
     * Build a query for principal outstanding within a date range.
     */
    private function buildPrincipalOutstandingQuery(string $endDate, int $maxDays, int $minDays): Builder
    {
        $query = LoanSchedule::query()
            ->selectRaw('sum(principal_remaining)')
            ->whereColumn('loan_id', 'loans.id')
            ->where('principal_remaining', '>', 0)
            ->whereDate('payment_due_date', '<', $endDate);

        if ($minDays === 0) {
            // Special case for 0-30 days range
            $query->whereBetween('payment_due_date', [
                Carbon::parse($endDate)->subDays($maxDays)->format('Y-m-d'),
                $endDate,
            ]);
        } elseif ($maxDays === 0) {
            $query->whereDate('payment_due_date', '<=', Carbon::parse($endDate)->subDays($minDays)->format('Y-m-d'));
        } else {
            $query->whereBetween('payment_due_date', [
                Carbon::parse($endDate)->subDays($maxDays)->format('Y-m-d'),
                Carbon::parse($endDate)->subDays($minDays)->format('Y-m-d'),
            ]);
        }

        return $query->limit(1);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function loan_product()
    {
        return $this->belongsTo(LoanProduct::class, 'Loan_Product_ID');
    }

    public function product()
    {
        return $this->belongsTo(LoanProduct::class, 'Loan_Product_ID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Customer_ID');
    }

    public function loan_application()
    {
        return $this->belongsTo(LoanApplication::class, 'Loan_Application_ID');
    }

    public function application()
    {
        return $this->belongsTo(LoanApplication::class, 'Loan_Application_ID');
    }

    public function loan_term()
    {
        return $this->belongsTo(LoanProductTerm::class, 'Loan_Term_ID');
    }

    public function term(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LoanProductTerm::class, 'Loan_Term_ID');
    }

    public function loan_repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class, 'Loan_ID');
    }

    public function write_offs(): HasMany
    {
        return $this->hasMany(WrittenOffLoan::class, 'Loan_ID');
    }

    public function schedule(): HasMany
    {
        return $this->hasMany(LoanSchedule::class);
    }

    public function loan_schedules(): HasMany
    {
        return $this->hasMany(LoanSchedule::class);
    }

    public function loan_repayment_schedule(): HasMany
    {
        return $this->hasMany(LoanSchedule::class);
    }

    public function latestOutstandingPayment(): HasOne
    {
        return $this->hasOne(LoanSchedule::class)
            ->ofMany(['payment_due_date' => 'min'], function ($query) {
                $query->where('total_outstanding', '>', 0);
            });
    }

    public function lastRepayment(): HasOne
    {
        return $this->hasOne(LoanRepayment::class)
            ->ofMany('Last_Payment_Date', 'max');
    }

    public function expiryDate()
    {
        return $this->schedule()->max('payment_due_date');
    }

    public function disbursement(): HasOne
    {
        return $this->hasOne(LoanDisbursement::class);
    }

    public function loan_disbursement(): HasOne
    {
        return $this->hasOne(LoanDisbursement::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class);
    }

    public function writtenOffBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'Written_Off_Officer', 'id');
    }

    public static function generateReference()
    {
        return 'GG-' . strtoupper(uniqid());
    }

    /**
     * @param $number_of_installments
     * @param $frequency_of_installments
     * @param $loanTermInDays
     * @param $startFrom - The date from which to start calculating the maturity date
     * @return \Illuminate\Support\Carbon
     * @throws Exception
     */
    public static function determineMaturityDate($number_of_installments, $frequency_of_installments, $loanTermInDays, $startFrom = null): \Illuminate\Support\Carbon
    {
        if (config('lms.loans.enable_ageing')) {
            $current_date = now()->subDays(config('lms.loans.back_date_days'));
        } else {
            $current_date = $startFrom ? \Illuminate\Support\Carbon::parse($startFrom) : now();
        }

        switch ($frequency_of_installments) {
            case 'Once':
                $maturity_date = $current_date->startOfDay()->addDays($loanTermInDays);
                break;
            case 'Daily':
                $maturity_date = $current_date->startOfDay()->addDays($number_of_installments);
                break;
            case 'DailyExcludingSundays':
                // Start counting days excluding Sundays
                $maturity_date = $current_date->startOfDay()->addDays(7);
                $daysAdded = 0;
                while ($daysAdded < $number_of_installments) {
                    $maturity_date->addDay();
                    // Only count non-Sundays
                    if ($maturity_date->dayOfWeek !== Carbon::SUNDAY) {
                        $daysAdded++;
                    }
                }
                break;
            case 'Weekly':
                $maturity_date = $current_date->startOfDay()->addWeeks($number_of_installments);
                break;
            case 'Bi-weekly':
                $maturity_date = $current_date->startOfDay()->addWeeks($number_of_installments * 2);
                break;
            case 'Monthly':
                $maturity_date = $current_date->startOfDay()->addMonths($number_of_installments);
                break;
            default:
                throw new Exception("Invalid frequency '$frequency_of_installments' of installments");
        }

        return $maturity_date;
    }

    public static function determineDateOfFirstPayment($frequency_of_installments, $loanTermInDays, $startFrom = null): \Illuminate\Support\Carbon
    {
        $disbursement_date = $startFrom ? \Illuminate\Support\Carbon::parse($startFrom) : now();

        switch ($frequency_of_installments) {
            case 'Once':
                $first_payment_date = $disbursement_date->startOfDay()->addDays($loanTermInDays);
                break;
            case 'Daily':
                $first_payment_date = $disbursement_date->startOfDay()->addDay();
                break;
            case 'DailyExcludingSundays':
                $first_payment_date = $disbursement_date->startOfDay()->addDay(7);
                break;
            case 'Weekly':
                $first_payment_date = $disbursement_date->startOfDay()->addWeek();
                break;
            case 'Bi-weekly':
                $first_payment_date = $disbursement_date->startOfDay()->addWeeks(2);
                break;
            case 'Monthly':
                $first_payment_date = $disbursement_date->startOfDay()->addMonth();
                break;
            default:
                throw new Exception('Invalid frequency of installments');
        }

        return $first_payment_date;
    }

    public function getOutstandingFees()
    {
        $total_fees = $this->schedule->lazy()->filter(function ($schedule) {
            return str($schedule->type)->contains('fee', true);
        })->sum('total_payment');

        $fees_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as fees_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.slug', 'like', '%fee%');
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->fees_paid ?? 0;

        $fees_written_off = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as fees_written_off')
            ->join('written_off_loans', function (JoinClause $join) {
                $join->on('written_off_loans.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', WrittenOffLoan::class)
                    ->where('Is_Recovered', 0);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.slug', 'like', '%fee%');
            })
            ->where('written_off_loans.Loan_ID', $this->id)
            ->where('written_off_loans.Customer_ID', $this->Customer_ID)
            ->first()->fees_written_off ?? 0;

        return $total_fees - ($fees_paid + $fees_written_off);
    }

    public function getOutstandingFeesExcludingWriteOffs()
    {
        if ($this->write_offs()->where('Is_Recovered', 0)->first() && !$this->loan_product->can_write_off_fees) {
            return 0;
        }

        $total_fees = $this->schedule->lazy()->filter(function ($schedule) {
            return str($schedule->type)->contains('fee', true);
        })->sum('total_payment');

        $fees_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as fees_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.slug', 'like', '%fee%');
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->fees_paid ?? 0;

        $fees_recovered = $this
            ->write_offs()
            ->where('Is_Recovered', 1)
            ->sum('fees');

        return $total_fees - ($fees_paid + $fees_recovered);
    }

    public function getOutstandingPenalties()
    {
        $total_penalties = $this->penalties->sum('Amount_To_Pay');

        $penalties_paid = JournalEntry::selectRaw('COALESCE(SUM(journal_entries.credit_amount), 0) as amount_paid')
            ->join('loan_repayments', function ($join) {
                $join->on('journal_entries.transactable_id', '=', 'loan_repayments.id')
                    // TODO Raymond: This should only match the `LoanRepayment` after
                    // penalty accrual transactions have been implemented.
                    ->whereIn('journal_entries.transactable', [
                        LoanRepayment::class,
                        LoanPenalty::class,
                    ]);
            })
            ->join('accounts', function ($join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->whereIn('accounts.slug', [
                        AccountSeederService::PENALTIES_FROM_LOAN_PAYMENTS_SLUG,
                    ]);
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->whereColumn('journal_entries.partner_id', 'loan_repayments.partner_id')
            ->value('amount_paid');

        $penalties_written_off = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as penalties_written_off')
            ->join('written_off_loans', function (JoinClause $join) {
                $join->on('written_off_loans.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', WrittenOffLoan::class)
                    ->where('Is_Recovered', 0);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->whereIn('accounts.slug', [
                        AccountSeederService::PENALTIES_FROM_LOAN_PAYMENTS_SLUG,
                    ]);
            })
            ->where('written_off_loans.Loan_ID', $this->id)
            ->where('written_off_loans.Customer_ID', $this->Customer_ID)
            ->first()->penalties_written_off ?? 0;

        return $total_penalties - ($penalties_paid + $penalties_written_off);
    }

    public function getOutstandingPenaltiesExcludingWriteOffs()
    {
        if ($this->write_offs()->where('Is_Recovered', 0)->first() && !$this->loan_product->can_write_off_penalties) {
            return 0;
        }

        $total_penalties = $this->penalties->sum('Amount_To_Pay');

        $penalties_paid = JournalEntry::selectRaw('COALESCE(SUM(journal_entries.credit_amount), 0) as amount_paid')
            ->join('loan_repayments', function ($join) {
                $join->on('journal_entries.transactable_id', '=', 'loan_repayments.id')
                    ->whereIn('journal_entries.transactable', [
                        LoanRepayment::class,
                        LoanPenalty::class,
                    ]);
            })
            ->join('accounts', function ($join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->whereIn('accounts.slug', [
                        AccountSeederService::PENALTIES_FROM_LOAN_PAYMENTS_SLUG,
                    ]);
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->whereColumn('journal_entries.partner_id', 'loan_repayments.partner_id')
            ->value('amount_paid');

        $penalties_recovered = $this
            ->write_offs()
            ->where('Is_Recovered', 1)
            ->sum('penalties');

        return $total_penalties - ($penalties_paid + $penalties_recovered);
    }

    public function fees()
    {
        return $this->hasMany(LoanFee::class, 'Loan_ID');
    }

    public function written_off_loan(): HasOne
    {
        return $this->hasOne(WrittenOffLoan::class, 'Loan_ID');
    }

    public function penalties()
    {
        return $this->hasMany(LoanPenalty::class, 'Loan_ID');
    }

    public function totalInterest()
    {
        return $this->schedule->sum('interest');
    }

    public function totalPrincipal()
    {
        return $this->schedule->sum('principal');
    }

    public function totalFees()
    {
        return $this->schedule()->where('type', 'like', '%fee%')->lazy()->sum('total_payment');
    }

    public function totalPenalties()
    {
        return $this->penalties->sum('Amount_To_Pay');
    }

    public function penaltiesPaid()
    {
        return $this->totalPenalties() - $this->getOutstandingPenalties();
    }

    public function totalAmountToPay()
    {
        return $this->totalPrincipal() + $this->totalInterest() + $this->totalPenalties();
    }

    public function totalPayment()
    {
        return $this->loan_repayments()->sum('amount');
    }

    public function dailyPayment()
    {
        return $this->totalRepayment() / $this->Number_of_Payments;
    }

    public function totalRepayment()
    {
        return $this->totalPayment();
    }

    public function totalToBePaid()
    {
        return $this->schedule->lazy()->sum('total_outstanding');
    }

    public function totalOutstandingBalance()
    {
        return $this->getOutstandingPrincipal()
            + $this->interestDue()
            + $this->getOutstandingFees()
            + $this->getOutstandingPenalties();
    }

    public function totalOutstandingBalanceExcludingWriteOffs()
    {
        return $this->getOutstandingPrincipalExcludingWriteOffs()
            + $this->getOutstandingInterestExcludingWriteOffs()
            + $this->getOutstandingFeesExcludingWriteOffs()
            + $this->getOutstandingPenaltiesExcludingWriteOffs();
    }

    public function getOutstandingInterest()
    {
        $total_interest = $this->schedule->sum('interest');

        $interest_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as interest_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->whereIn('accounts.slug', [
                        AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG,
                    ]);
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->interest_paid ?? 0;

        $interest_written_off = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as interest_written_off')
            ->join('written_off_loans', function (JoinClause $join) {
                $join->on('written_off_loans.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', WrittenOffLoan::class)
                    ->where('Is_Recovered', 0);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->whereIn('accounts.slug', [
                        AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG,
                    ]);
            })
            ->where('written_off_loans.Loan_ID', $this->id)
            ->where('written_off_loans.Customer_ID', $this->Customer_ID)
            ->first()->interest_written_off ?? 0;

        return $total_interest - ($interest_paid + $interest_written_off);
    }

    public function getOutstandingInterestExcludingWriteOffs()
    {
        if ($this->write_offs()->where('Is_Recovered', 0)->first() && !$this->loan_product->can_write_off_interest) {
            return 0;
        }

        $total_interest = $this->schedule->sum('interest');

        $interest_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as interest_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->whereIn('accounts.slug', [
                        AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG,
                    ]);
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->interest_paid ?? 0;

        $interest_recovered = $this
            ->write_offs()
            ->where('Is_Recovered', 1)
            ->sum('interest');

        return $total_interest - ($interest_paid + $interest_recovered);
    }

    public function interestDue()
    {
        // Get the first installment
        $firstInterests = $this->schedule()
            ->where('installment_number', 1)
            ->whereNotNull('interest')
            ->select('id')
            ->get();

        $firstInterestIds = $firstInterests->pluck('id');

        $firstInterestDetails = $this->schedule()
            ->whereIn('id', $firstInterestIds)
            ->get();

        $firstInterestsTotal = $firstInterestDetails->sum('interest');

        // Get the sum of interest for installments where payment_due_date is <= now()
        // and exclude the first installment
        $endDate = now();

        $interestDue = $this->schedule()
            ->where('payment_due_date', '<=', $endDate)
            ->whereNotIn('id', $firstInterestIds) // Exclude firstFeeIds
            ->sum('interest');

        $interest_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as interest_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->whereIn('accounts.slug', [
                        AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG,
                    ]);
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->interest_paid ?? 0;

        return max(0, ($interestDue + $firstInterestsTotal) - $interest_paid);
    }

    public function principalDue()
    {
        return $this->schedule
            ->where('payment_due_date', '<=', now())
            ->lazy()
            ->sum('principal_remaining');
    }

    public function feesDue()
    {
        // Get the first fee for each installment
        $firstFees = $this->schedule()
            ->where('type', 'like', '%fee%')
            ->where('installment_number', 1)
            ->select('id')
            ->get();
        // Extract the IDs of the first fees
        $firstFeeIds = $firstFees->pluck('id');
        // Retrieve the full details of the first fees
        $firstFeeDetails = $this->schedule()
            ->whereIn('id', $firstFeeIds)
            ->get();

        // Sum the total_outstanding for the first fees
        $firstFeesTotal = $firstFeeDetails->sum('total_outstanding');
        // Get the sum of fees where payment_due_date is <= now()
        $feesDue = $this->schedule()
            ->where('type', 'like', '%fee%')
            ->where('payment_due_date', '<=', now())
            ->whereNotIn('id', $firstFeeIds) // Exclude firstFeeIds
            ->sum('total_outstanding');
        // Ensure the first fees are included

        return $feesDue + $firstFeesTotal;
    }

    public function feesDueExcludingWriteOffs()
    {
        if ($this->write_offs()->where('Is_Recovered', 0)->first() && !$this->loan_product->can_write_off_fees) {
            return 0;
        }

        // Get the first fee for each installment
        $firstFees = $this->schedule()
            ->where('type', 'like', '%fee%')
            ->where('installment_number', 1)
            ->select('id')
            ->get();
        // Extract the IDs of the first fees
        $firstFeeIds = $firstFees->pluck('id');
        // Retrieve the full details of the first fees
        $firstFeeDetails = $this->schedule()
            ->whereIn('id', $firstFeeIds)
            ->get();

        // Sum the total_outstanding for the first fees
        $firstFeesTotal = $firstFeeDetails->sum('total_payment');
        // Get the sum of fees where payment_due_date is <= now()
        $feesDue = $this->schedule()
            ->where('type', 'like', '%fee%')
            ->where('payment_due_date', '<=', now())
            ->whereNotIn('id', $firstFeeIds) // Exclude firstFeeIds
            ->sum('total_payment');
        // Ensure the first fees are included

        $fees_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as fees_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.slug', 'like', '%fee%');
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->fees_paid ?? 0;

        return max(0, ($feesDue + $firstFeesTotal) - $fees_paid);
    }

    public function getOutstandingPrincipal()
    {
        $total_principal = $this->schedule->sum('principal');
        $principal_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as principal_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.accountable_type', LoanProduct::class);
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->principal_paid ?? 0;

        $principal_written_off = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as principal_written_off')
            ->join('written_off_loans', function (JoinClause $join) {
                $join->on('written_off_loans.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', WrittenOffLoan::class)
                    ->where('Is_Recovered', 0);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.accountable_type', LoanProduct::class);
            })
            ->where('written_off_loans.Loan_ID', $this->id)
            ->where('written_off_loans.Customer_ID', $this->Customer_ID)
            ->first()->principal_written_off ?? 0;

        return $total_principal - ($principal_paid + $principal_written_off);
    }

    public function getOutstandingPrincipalExcludingWriteOffs()
    {
        $total_principal = $this->schedule->sum('principal');
        $principal_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as principal_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.accountable_type', LoanProduct::class);
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->principal_paid ?? 0;

        $principal_recovered = $this
            ->write_offs()
            ->where('Is_Recovered', 1)
            ->sum('Amount_Written_Off');

        return $total_principal - ($principal_paid + $principal_recovered);
    }

    public function isRecurring()
    {
        return $this->Number_of_Payments > 1;
    }

    public function isOverdue(): bool
    {
        return $this->Credit_Account_Status == self::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS;
    }

    public function isCleared(): bool
    {
        return $this->totalOutstandingBalance() == 0;
    }

    public function isWrittenOff(): bool
    {
        return $this->Credit_Account_Status === self::ACCOUNT_STATUS_WRITTEN_OFF;
    }

    public function isWithinTerms(): bool
    {
        return $this->Credit_Account_Status === self::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS;
    }

    public function hasMaturedToday(): bool
    {
        return $this->Maturity_Date->isToday();
    }
    public function canWriteOff(): bool
    {
        return $this->Maturity_Date->isBefore(now()) && in_array($this->Credit_Account_Status, [
            LoanAccountType::BeyondTerms->value,
            LoanAccountType::WithinTerms->value,
        ]) && $this->isCleared() === false;
    }

    public function getStatusAttribute()
    {
        return self::SUPPORTED_Credit_Account_Statuses[$this->Credit_Account_Status];
    }

    public static function determineNumberOfPayments($loanTerm, $frequencyOfPayment): int
    {
        switch ($frequencyOfPayment) {
            case 'Once':
                return 1;
            case 'Daily':
                return $loanTerm; // Assuming loan term is in days
            case 'DailyExcludingSundays':
                // Calculate number of Sundays in the loan period
                $startDate = new DateTime; // assuming loan starts today
                $endDate = clone $startDate;
                $endDate->add(new DateInterval("P{$loanTerm}D"));

                $sundays = 0;
                $interval = new DateInterval('P1D');
                $period = new DatePeriod($startDate, $interval, $endDate);

                foreach ($period as $day) {
                    if ($day->format('w') == 0) { // 0 = Sunday
                        $sundays++;
                    }
                }

                return $loanTerm - $sundays;
            case 'Weekly':
                return floor($loanTerm / 7);
            case 'Bi-weekly':
                return floor($loanTerm / 14);
            case 'Monthly':
                return floor($loanTerm / 30);
            default:
                throw new Exception("Invalid repayment frequency: $frequencyOfPayment");
        }
    }

    public function productPenalties()
    {
        return $this->product->penalties;
    }

    public function getOutstandingDays()
    {
        $lastUnPaidDate = $this->schedule()
            ->where('payment_due_date', '<', Carbon::parse(now()))
            ->where('total_outstanding', '>', 0)->min('payment_due_date');

        $lastUnPaidDate = Carbon::parse($lastUnPaidDate);

        return (int) now()->diffInDays($lastUnPaidDate, true);
    }

    public function getAmountDue()
    {
        if ($this->isWrittenOff()) {
            return $this->Written_Off_Amount - $this->Written_Off_Amount_Recovered;
        }
        $today = date('Y-m-d');

        return $this->schedule()
            ->where('payment_due_date', '<=', $today)
            ->sum('total_outstanding') + $this->getOutstandingPenalties();
    }

    public function dueDate()
    {
        return $this->schedule()
            ->where('total_outstanding', '>', 0)
            ->min('payment_due_date');
    }

    public function daysOverdue()
    {
        $maxDate = Carbon::parse(now());
        $minDate = Carbon::parse($this->dueDate());

        return round($minDate->diffInDays($maxDate));
    }

    public function overDueAmount()
    {
        $today = date('Y-m-d');

        return $this->schedule()
            ->where('payment_due_date', '<', $today)
            ->where('total_outstanding', '>', 0)
            ->sum('total_outstanding');
    }

    public function totalFeesPaid()
    {
        $fees_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as fees_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.slug', 'like', '%fee%');
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->fees_paid ?? 0;

        $fees_recovered = $this
            ->write_offs()
            ->where('Is_Recovered', 1)
            ->sum('fees');

        return $fees_paid + $fees_recovered;
    }

    public function totalPrincipalPaid()
    {
        $principal_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as principal_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.accountable_type', LoanProduct::class);
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->principal_paid ?? 0;

        $principal_recovered = $this
            ->write_offs()
            ->where('Is_Recovered', 1)
            ->sum('Amount_Written_Off');

        return $principal_paid + $principal_recovered;
    }

    public function totalInterestPaid()
    {
        $interest_paid = JournalEntry::selectRaw('COALESCE(SUM(credit_amount), 0) as interest_paid')
            ->join('loan_repayments', function (JoinClause $join) {
                $join->on('loan_repayments.id', '=', 'journal_entries.transactable_id')
                    ->where('journal_entries.transactable', LoanRepayment::class);
            })
            ->join('accounts', function (JoinClause $join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->whereIn('accounts.slug', [
                        AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG,
                    ]);
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->first()->interest_paid ?? 0;

        $interest_recovered = $this
            ->write_offs()
            ->where('Is_Recovered', 1)
            ->sum('interest');

        return $interest_paid + $interest_recovered;
    }

    public function totalPenaltiesPaid()
    {
        $penalties_paid = JournalEntry::selectRaw('COALESCE(SUM(journal_entries.credit_amount), 0) as amount_paid')
            ->join('loan_repayments', function ($join) {
                $join->on('journal_entries.transactable_id', '=', 'loan_repayments.id')
                    ->whereIn('journal_entries.transactable', [
                        LoanRepayment::class,
                        LoanPenalty::class,
                    ]);
            })
            ->join('accounts', function ($join) {
                $join->on('accounts.id', '=', 'journal_entries.account_id')
                    ->whereIn('accounts.slug', [
                        AccountSeederService::PENALTIES_FROM_LOAN_PAYMENTS_SLUG,
                    ]);
            })
            ->where('loan_repayments.Loan_ID', $this->id)
            ->where('loan_repayments.Customer_ID', $this->Customer_ID)
            ->whereColumn('journal_entries.partner_id', 'loan_repayments.partner_id')
            ->value('amount_paid');

        $penalties_recovered = $this
            ->write_offs()
            ->where('Is_Recovered', 1)
            ->sum('interest');

        return $penalties_paid + $penalties_recovered;
    }

    public function getDailyRepayment()
    {
        if ($this->Number_of_Payments == 0) {
            return 0;
        }

        return $this->totalToBePaid() / $this->Number_of_Payments;
    }

    /**
     * Create loan fees for the given loan.
     */
    public static function createLoanFees(Loan $loan)
    {
        $loanProduct = $loan->product;
        $fees = $loanProduct->fees;
        $numberOfInstallments = $loan->Number_of_Payments;
        $collectionFees = [];

        foreach ($fees as $fee) {
            if ($fee->Applicable_At == 'Maturity') {
                // Skip fees that are applicable only at application if the loan is not approved
                continue;
            }
            if ($fee->Applicable_On === 'Installment Balance') {
                $collectionFees[] = $fee;

                continue;
            }

            if ($fee->Calculation_Method === 'Tiered') {
                $split = self::calculateTieredAmounts($fee, $loan->Credit_Amount);

                if ($fee->Applicable_At === 'Repayment') {
                    $split = array_map(fn($v) => $v * $numberOfInstallments, $split);
                }

                // Insert fee for Platform (payableAmount)
                self::storeLoanFee($loan, $fee, $split['platform'], $fee->Applicable_At, null, true);

                // Insert fee for Loan Provider (value - payableAmount)
                self::storeLoanFee($loan, $fee, $split['provider'], $fee->Applicable_At, null, false);

                continue;
            }

            // Flat or Percentage fees
            $amountToPay = self::calculateFeeAmount($fee, $loan);

            if ($fee->Applicable_At === 'Repayment') {
                $amountToPay *= $numberOfInstallments;
            }

            self::storeLoanFee($loan, $fee, $amountToPay, $fee->Applicable_At, null, !!$fee->Payable_Account_ID);
        }

        // Process deferred installment balance fees
        foreach ($collectionFees as $fee) {
            $feeAmount = 0;

            if ($fee->Calculation_Method === 'Percentage') {
                $feeAmount = $loan->Credit_Amount * ($fee->Value / 100);
            }

            self::storeLoanFee($loan, $fee, $feeAmount, $fee->Applicable_At, 0);
        }
    }

    /**
     * Handle flat or percentage-based fee calculation.
     */
    protected static function calculateFeeAmount($fee, Loan $loan): float
    {
        $value = $fee->Value;
        $method = $fee->Calculation_Method;
        $baseAmount = $loan->Credit_Amount;

        return match ($method) {
            'Percentage' => ($fee->Applicable_On === 'Principal') ? $baseAmount * ($value / 100) : $value,
            default => $value,
        };
    }

    /**
     * Calculate split amounts for tiered fees.
     */
    protected static function calculateTieredAmounts($fee, float $baseAmount): array
    {
        $tiers = json_decode($fee->Tiers, true) ?? [];

        foreach ($tiers as $tier) {
            if ($baseAmount >= $tier['min'] && $baseAmount <= $tier['max']) {
                $value = $tier['value'] ?? 0;
                $payableAmount = $tier['payableAmount'] ?? 0;

                return [
                    'platform' => $payableAmount,
                    'provider' => $value - $payableAmount,
                ];
            }
        }

        return ['platform' => 0, 'provider' => 0];
    }

    /**
     * Create a LoanFee record.
     */
    protected static function storeLoanFee(
        Loan $loan,
        $fee,
        float $amountToPay,
        string $chargeAt,
        ?float $amount = null,
        bool $forPlatform = false
    ): void {
        LoanFee::create([
            'partner_id' => $loan->partner->id,
            'Loan_Product_ID' => $loan->product->id,
            'Loan_Product_Fee_ID' => $fee->id,
            'Amount_To_Pay' => $amountToPay,
            'Amount' => $amount ?? (($chargeAt === 'Application' || $chargeAt === 'Disbursement') ? $amountToPay : 0),
            'Charge_At' => $chargeAt,
            'Payable_Account_ID' => $forPlatform ? $fee->Payable_Account_ID : null,
            'Customer_ID' => $loan->customer->id,
            'Loan_ID' => $loan->id,
            'Status' => ($chargeAt === 'Application' || $chargeAt === 'Disbursement') ? LoanFee::FULLY_PAID : LoanFee::NOT_PAID,
            'is_part_of_interest' => $fee->is_part_of_interest
        ]);
    }

    public function getAmountWrittenOff()
    {
        $amount = LoanRepayment::where('Credit_Account_Status', self::ACCOUNT_STATUS_WRITTEN_OFF)->get('Current_Balance_Amount', '')->toArray();

        return $amount[0];
    }

    public function getOverdueAmount(string|\Illuminate\Support\Carbon|null $asAt = null)
    {
        if (is_null($asAt)) {
            $asAt = now();
        }

        if ($this->Credit_Payment_Frequency === CreditPaymentFrequency::Monthly->name && $this->Number_of_Payments === 1) {
            // We are dealing with a monthly loan that has only one expected payment
            return $this->schedule->sum('total_outstanding');
        }

        return $this->schedule->where('payment_due_date', '<', $asAt)->sum('total_outstanding');
    }

    public function daysToExpiry(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (now()->isAfter($this->Maturity_Date)) {
                    return 0;
                }

                return round(now()->diffInDays($this->Maturity_Date, true));
            },
        );
    }

    /**
     * @deprecated Use $this->account_number instead
     */
    public function formattedId(): Attribute
    {
        return Attribute::make(
            get: fn() => 'L' . str($this->id)->padLeft(5, '0')
        );
    }

    public function accountNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->id < 10000000) {
                    return 'L1' . str($this->id)->padLeft(7, '0');
                }

                return 'L' . $this->id;
            }
        );
    }

    /**
     * Static method to map database status to API status
     */
    public static function mapToApiStatus(int $dbStatus): string
    {
        $mapping = [
            self::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS => self::API_STATUS_APPROVED,
            self::ACCOUNT_STATUS_FULLY_PAID_OFF => self::API_STATUS_LIQ,
            self::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS => self::API_STATUS_APPROVED,
        ];

        return $mapping[$dbStatus];
    }
}
