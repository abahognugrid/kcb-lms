<?php

namespace App\Models;

use App\Models\Accounts\Account;
use App\Models\Scopes\PartnerScope;
use App\Models\Transactables\BaseTransaction;
use App\Models\Transactables\Contracts\Transactable;
use App\Notifications\SmsNotification;
use App\Services\Account\AccountSeederService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @property \Illuminate\Support\Carbon Transaction_Date
 */
class LoanRepayment extends BaseTransaction implements Transactable
{
    use HasFactory, SoftDeletes;

    // Constants for account status codes
    const STATUS_OUTSTANDING_BEYOND_TERMS = 1;

    const STATUS_WRITTEN_OFF = 3;

    const STATUS_FULLY_PAID = 4;

    const STATUS_CURRENT_WITHIN_TERMS = 5;

    const STATUS_WRITTEN_OFF_RECOVERY = 6;

    const STATUS_FORFEITURE = 7;

    protected $fillable = [
        'Loan_ID',
        'Customer_ID',
        'partner_id',
        'Transaction_ID',
        'amount',
        'Principal',
        'Interest',
        'Fee',
        'Penalty',
        'Transaction_Date',
        'Current_Balance_Amount',
        'Current_Balance_Amount_UGX_Equivalent',
        'Current_Balance_Indicator',
        'Last_Payment_Date',
        'Last_Payment_Amount',
        'Credit_Account_Status',
        'Last_Status_Change_Date',
        'Credit_Account_Risk_Classification',
        'Credit_Account_Arrears_Date',
        'Number_of_Days_in_Arrears',
        'Balance_Overdue',
        'Risk_Classification_Criteria',
        'Opening_Balance_Indicator ',
        'Annual_Interest_Rate_at_Reporting',
        'partner_notified',
        'partner_notified_date',
    ];

    protected function casts()
    {
        return [
            'Transaction_Date' => 'datetime',
            'Last_Payment_Date' => 'datetime',
            'Last_Status_Change_Date' => 'datetime',
            'Credit_Account_Arrears_Date' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    /**
     * Create a new loan payment
     */
    public static function createPayment(Loan $loan, float $amount): self
    {
        $accountStatus = self::determineAccountStatus($loan);

        return self::query()->create([
            'Loan_ID' => $loan->id,
            'Customer_ID' => $loan->Customer_ID,
            'partner_id' => $loan->partner_id,
            'amount' => $amount,
            'Transaction_Date' => Carbon::now(),
            'Last_Payment_Date' => Carbon::now(),
            'Last_Payment_Amount' => $amount,
            'Credit_Account_Status' => $accountStatus,
            'Current_Balance_Amount' => $loan->totalOutstandingBalance() - $amount,
            'Current_Balance_Amount_UGX_Equivalent' => $loan->totalOutstandingBalance() - $amount,
        ]);
    }

    /**
     * Determine the account status based on loan state
     */
    private static function determineAccountStatus(Loan $loan): int
    {
        /**
         * We are checking for written off first because
         * this is marked as a recovery other than the usual repayment
         */
        if ($loan->isWrittenOff()) {
            return self::STATUS_WRITTEN_OFF;
        }
        if ($loan->isCleared()) {
            return self::STATUS_FULLY_PAID;
        }
        if ($loan->isOverdue()) {
            return self::STATUS_OUTSTANDING_BEYOND_TERMS;
        }

        return self::STATUS_CURRENT_WITHIN_TERMS;
    }

    /**
     * Determine the account status based on loan state
     */
    private static function determineClosureReason(Loan $loan): int
    {
        // if loan is beyond terms return 1
        if ($loan->isWrittenOff()) {
            return 3;
        }
        if ($loan->isOverdue()) {
            return 1;
        }
        if ($loan->isWithinTerms()) {
            return 4;
        }
        if ($loan->hasMaturedToday()) {
            return 2;
        }

        return 5;
    }

    // Relationships
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'Loan_ID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Customer_ID');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function transaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function relatedPayments()
    {
        return $this->hasMany(LoanRepayment::class, 'Loan_ID', 'Loan_ID');
    }

    public function journalEntries(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'journable', 'transactable', 'transactable_id')->chaperone();
    }

    public function amount(): float
    {
        return $this->amount;
    }

    /**
     * @throws Exception
     */
    protected function makeJournalEntries(): void
    {
        $this->processPayment();
    }

    /**
     * Main payment processing logic
     * @throws Exception
     */
    private function processPayment(): void
    {
        $accounts = $this->getRequiredAccounts();
        $paymentAmount = $this->amount();
        $initialPaymentAmount = $paymentAmount;
        $repaymentOrder = $this->getRepaymentOrder();

        $schedules = $this->getLoanSchedules();

        $loanSchedules = $schedules->filter(function ($item) {
            return !str_contains($item->type, 'Fee');
        });

        $feeSchedules = $schedules->filter(function ($item) {
            return str_contains($item->type, 'Fee');
        });

        // First try to process penalties if any exist
        $penalties = $this->loan->getOutstandingPenalties();
        if ($penalties > 0 && $paymentAmount > 0) {
            $penaltyAmount = min($paymentAmount, $penalties);
            $penaltySchedule = $schedules->first(); // or get appropriate schedule

            $this->handlePayment(
                $penaltySchedule,
                'Penalty',
                $accounts,
                $penaltyAmount
            );
            $paymentAmount -= $penaltyAmount;
        }

        // Then process regular repayment order
        foreach ($loanSchedules as $schedule) {
            if ($paymentAmount <= 0) {
                break;
            }

            $current_due_date = $schedule->payment_due_date;

            $allCleared = true;

            foreach ($repaymentOrder as $payable) {
                if ($paymentAmount <= 0) {
                    break;
                }

                if ($payable === 'Fees') {
                    if ($paymentAmount <= 0) {
                        break;
                    }

                    $feeSchedules->filter(function ($item) use ($current_due_date) {
                        return $item->payment_due_date === $current_due_date;
                    });

                    foreach ($feeSchedules as $fee_schedule) {
                        $this->processPayable(
                            $fee_schedule,
                            $payable,
                            $accounts,
                            $paymentAmount,
                            $allCleared
                        );
                    }

                    continue;
                }

                $this->processPayable(
                    $schedule,
                    $payable,
                    $accounts,
                    $paymentAmount,
                    $allCleared
                );
            }

            if (!$allCleared) {
                break;
            }
        }

        // Final check for overpayment
        if ($paymentAmount > 0) {
            $errorMessage = "Overpayment detected. Initial payment: $initialPaymentAmount, " .
                "Amount remaining after processing: $paymentAmount" . "Partner ID: {$this->partner_id}, Loan ID: {$this->Loan_ID}, Customer ID: {$this->Customer_ID}, Transaction ID: {$this->Transaction_ID}, Payment ID: {$this->id}";
            Log::debug($errorMessage);

            $this->recordGeneralLedgerEntry(
                $accounts['over_payment'],
                $paymentAmount,
                'Credit',
                'Cash In',
            );

            $this->recordGeneralLedgerEntry(
                $accounts['collection_ova'],
                $paymentAmount,
                'Debit',
                'Cash In',
            );
        }

        $this->updateLoanStatus();
    }

    /**
     * Process a single payable item for a schedule
     */
    private function processPayable(
        LoanSchedule $schedule,
        string $payable,
        array $accounts,
        float &$paymentAmount,
        bool &$allCleared
    ): void {
        if (($outstandingAmount = $this->getOutstandingAmountForSchedule($schedule, $payable)) > 0) {
            $amountToPay = min($paymentAmount, $outstandingAmount);

            if ($amountToPay <= 0) {
                return;
            }

            $this->handlePayment(
                $schedule,
                $payable,
                $accounts,
                $amountToPay
            );

            if ($this->shouldDeductPaymentAmount($schedule, $payable)) {
                $paymentAmount -= $amountToPay;
            }

            if ($this->getOutstandingAmountForSchedule($schedule, $payable) > 0) {
                $allCleared = false;
            }

            if ($paymentAmount <= 0) {
                return;
            }
        }
    }

    /**
     * Handle payment accounting and application
     */
    private function handlePayment(
        LoanSchedule $schedule,
        string $payable,
        array $accounts,
        float $amountToPay
    ): void {
        // Normalize payable type for fees
        $payableType = str_contains($schedule->type, 'Fee') ? 'Fees' : $payable;

        $this->processAccounting(
            $schedule->payable_to,
            $payableType,
            $schedule->type,
            $accounts,
            $amountToPay
        );

        $this->applyPaymentToSchedule($schedule, $payableType, $amountToPay);
    }

    /**
     * Get required accounts for transaction processing
     */
    private function getRequiredAccounts(): array
    {
        $loanProduct = $this->loan->loan_product;

        return [
            'interest' => Account::where('partner_id', $loanProduct->partner_id)
                ->where('slug', AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG)
                ->first(),
            'penalties' => Account::where('partner_id', $loanProduct->partner_id)
                ->where('slug', AccountSeederService::PENALTIES_FROM_LOAN_PAYMENTS_SLUG)
                ->first(),
            'fees' => Account::where('partner_id', $loanProduct->partner_id)
                ->where('slug', AccountSeederService::INCOME_FROM_FINES_SLUG)
                ->first(),
            'loan_product' => $loanProduct->general_ledger_account,
            'collection_ova' => Account::where('partner_id', $loanProduct->partner_id)
                ->where('slug', AccountSeederService::COLLECTION_OVA_SLUG)
                ->first(),
            'over_payment' => Account::where('partner_id', $loanProduct->partner_id)
                ->where('slug', AccountSeederService::LOAN_OVER_PAYMENTS_SLUG)
                ->first(),
        ];
    }

    /**
     * Get the repayment order from loan product
     */
    private function getRepaymentOrder(): array
    {
        $repaymentOrder = $this->loan->loan_product->Repayment_Order;

        // If it's a string, try to decode it as JSON
        if (is_string($repaymentOrder)) {
            $repaymentOrder = json_decode($repaymentOrder, true);
        }

        // If it's not an array (either because decoding failed or original wasn't an array), use default
        return is_array($repaymentOrder) ? $repaymentOrder : ['Principal', 'Interest'];
    }

    /**
     * Get loan schedules ordered by installment number
     */
    private function getLoanSchedules()
    {
        return LoanSchedule::where('loan_id', $this->loan->id)
            ->orderBy('installment_number')
            ->get();
    }

    /**
     * Update loan status after payment
     */
    public function updateLoanStatus(): void
    {
        $loan = $this->loan;
        $accountStatus = self::determineAccountStatus($loan);

        $this->Credit_Account_Status = $accountStatus;
        $this->save();

        if ($accountStatus == self::STATUS_FULLY_PAID) {
            $loan->Credit_Account_Closure_Date = Carbon::now();
            $loan->Credit_Account_Closure_Reason = $this->determineClosureReason($loan);
        }
        $loan->Credit_Account_Status = $accountStatus;
        $loan->Last_Status_Change_Date = Carbon::now();
        $loan->save();
    }

    /**
     * Get outstanding amount for a schedule item
     */
    private function getOutstandingAmountForSchedule(LoanSchedule $schedule, string $payable): float
    {
        switch ($payable) {
            case 'Fees':
                return str_contains($schedule->type, 'Fee') ? $schedule->total_outstanding : 0;
            case 'Interest':
                return $schedule->interest_remaining;
            case 'Principal':
                return $schedule->principal_remaining;
            default:
                return 0;
        }
    }

    /**
     * Check if a schedule should be processed
     */
    private function shouldProcessSchedule(LoanSchedule $schedule, string $payable): bool
    {
        $isFeeSchedule = str_contains($schedule->type, 'Fee');

        if ($isFeeSchedule) {
            return $payable === 'Fees';
        }
        return $schedule->payment_due_date <= now() || $schedule->installment_number == 1;
    }

    /**
     * Check if payment amount should be deducted
     */
    private function shouldDeductPaymentAmount(LoanSchedule $schedule, string $payable): bool
    {
        $isFeeSchedule = str_contains($schedule->type, 'Fee');

        if ($isFeeSchedule) {
            return $payable === 'Fees';
        }
        return $schedule->payment_due_date <= now() || $payable == 'Principal' || $schedule->installment_number == 1;
    }

    /**
     * Apply payment to a schedule item
     */
    private function applyPaymentToSchedule(LoanSchedule $schedule, string $payable, float $amount): void
    {
        switch ($payable) {
            case 'Interest':
                $this->applyInterestPayment($schedule, $amount);
                break;
            case 'Principal':
                $schedule->principal_remaining = max(0, $schedule->principal_remaining - $amount);
                break;
            case 'Fees':
                $schedule->total_outstanding = max(0, $schedule->total_outstanding - $amount);
                break;
        }

        $this->updateScheduleBalance($schedule, $payable);
    }

    /**
     * Apply interest payment with special handling for recurring loans
     */
    private function applyInterestPayment(LoanSchedule $schedule, float $amount): void
    {
        if ($schedule->payment_due_date >= now() && $schedule->loan->isRecurring()) {
            $schedule->interest_remaining = 0;
        } else {
            $schedule->interest_remaining = max(0, $schedule->interest_remaining - $amount);
        }
    }

    /**
     * Update schedule balance after payment
     */
    private function updateScheduleBalance(LoanSchedule $schedule, string $payable): void
    {
        $remainingBalance = ($payable == 'Fees')
            ? $schedule->total_outstanding
            : $schedule->principal_remaining + $schedule->interest_remaining;

        $schedule->total_outstanding = max(0, $remainingBalance);
        $schedule->save();
    }

    /**
     * Process accounting for a payment
     */
    private function processAccounting(
        $payableTo,
        string $payable,
        string $type,
        array $accounts,
        float $amount
    ): void {
        $accountingDetails = $this->getAccountingDetails($payableTo, $payable, $type, $accounts, $amount);
        if ($accountingDetails['gla']) {
            $this->recordCollectionOvaEntry($accounts, $amount);
            $this->recordGeneralLedgerEntry(
                $accountingDetails['gla'],
                $amount,
                $accountingDetails['accounting_type'],
                $accountingDetails['cash_type'],
            );
        }

        $this->updateFeeOrPenaltyStatus($payable, $type, $amount);
    }

    /**
     * Get accounting details for a payable type
     */
    private function getAccountingDetails($payableTo, string $payable, string $type, array $accounts, float $amount): array
    {
        $details = [
            'gla' => null,
            'cash_type' => 'Cash In',
            'accounting_type' => 'Credit',
            'debit_amount' => 0,
            'credit_amount' => $amount,
        ];

        switch ($payable) {
            case 'Fees':
                $details['gla'] = $payableTo ? Account::find($payableTo) : $accounts['fees'];
                break;
            case 'Interest':
                $details['gla'] = $accounts['interest'];
                break;
            case 'Penalty':
                $details['gla'] = $accounts['penalties'];
                break;
            case 'Principal':
                $details = $this->getPrincipalAccountingDetails($type, $accounts, $amount);
                break;
        }

        return $details;
    }

    /**
     * Get accounting details for principal payments
     */
    private function getPrincipalAccountingDetails(string $type, array $accounts, float $amount): array
    {
        $details = [
            'gla' => $accounts['loan_product'],
            'cash_type' => 'Cash In',
            'accounting_type' => 'Credit',
            'debit_amount' => 0,
            'credit_amount' => $amount,
        ];

        return $details;
    }

    /**
     * Record collection OVA entry
     */
    private function recordCollectionOvaEntry(array $accounts, float $amount): void
    {
        $this->recordGeneralLedgerEntry(
            $accounts['collection_ova'],
            $amount,
            'Debit',
            'Cash In',
        );
    }

    /**
     * Record a general ledger entry
     */
    private function recordGeneralLedgerEntry(
        Account $account,
        float $amount,
        string $accountingType,
        string $cashType,
    ): void {
        $this->transactions[] = JournalEntry::make(
            Str::of($accountingType)->lower(),
            $this->partner_id,
            $this->Customer_ID,
            $account->id,
            $account->name,
            $amount,
            $cashType,
        );
    }

    /**
     * Update fee or penalty status after payment
     */
    private function updateFeeOrPenaltyStatus(string $payable, string $type, float $amount): void
    {
        $loan = $this->loan;

        if ($payable === 'Fees') {
            $this->updateFeeStatus($loan, $type, $amount);
        } elseif ($payable === 'Penalty') {
            $this->updatePenaltyStatus($loan, $amount);
        }
    }

    /**
     * Update fee status after payment
     */
    private function updateFeeStatus(Loan $loan, string $type, float $amount): void
    {
        $fees = $loan->fees->whereIn('Charge_At', ['Repayment', 'Maturity'])->where('Status', '!=', LoanFee::FULLY_PAID);

        foreach ($fees as $fee) {
            if ($fee->loan_product_fee->Name == $type) {
                $amountToPay = min($fee->Amount_To_Pay, $amount);
                $fee->Amount = $amountToPay;
                $fee->Status = $this->determineFeeStatus($fee);
                $fee->save();
            }
        }
    }

    /**
     * Update penalty status after payment
     */
    private function updatePenaltyStatus(Loan $loan, float $amount): void
    {
        $penalties = $loan->penalties->where('Status', '!=', LoanFee::FULLY_PAID);

        foreach ($penalties as $penalty) {
            $amountToPay = min($penalty->Amount_To_Pay, $amount);
            $penalty->Amount = $amountToPay;
            $penalty->Status = $this->determinePenaltyStatus($penalty);
            $penalty->save();
        }
    }

    /**
     * Determine fee status based on payment
     */
    private function determineFeeStatus(LoanFee $fee): string
    {
        if ($fee->Amount == $fee->Amount_To_Pay) {
            return LoanFee::FULLY_PAID;
        }
        if ($fee->Amount > 0 && $fee->Amount < $fee->Amount_To_Pay) {
            return LoanFee::PARTIALLY_PAID;
        }

        return $fee->Status;
    }

    /**
     * Determine penalty status based on payment
     */
    private function determinePenaltyStatus(LoanPenalty $penalty): string
    {
        if ($penalty->Amount == $penalty->Amount_To_Pay) {
            return LoanPenalty::FULLY_PAID;
        }
        if ($penalty->Amount > 0 && $penalty->Amount < $penalty->Amount_To_Pay) {
            return LoanPenalty::PARTIALLY_PAID;
        }

        return $penalty->Status;
    }
}
