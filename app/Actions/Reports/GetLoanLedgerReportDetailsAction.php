<?php

namespace App\Actions\Reports;

use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\LoanRepayment;
use App\Models\LoanPenalty;
use App\Models\LoanFee;
use App\Models\LoanSchedule;
use App\Models\LoanApplication;
use App\Services\Account\AccountSeederService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetLoanLedgerReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $loanId = 0;
    protected int $perPage = 0;

    public function execute()
    {
        $loan = Loan::query()
            ->with([
                'loan_application',
                'disbursement',
                'loan_repayments',
                'schedule',
                'penalties'
            ])
            ->find($this->loanId);

        if (!$loan) {
            return $this->paginateCollection(collect([]), $this->perPage);
        }

        $ledgerEntries = collect();

        // Initialize running balances
        $runningBalances = [
            'principal_balance' => 0,
            'interest_balance' => 0,
            'fees_balance' => 0,
            'penalty_balance' => 0,
            'total_balance' => 0
        ];

        // 1. Add Loan Application entry
        if ($loan->loan_application) {
            $applicationEntry = $this->createLoanApplicationEntry($loan, $runningBalances);
            if ($this->isWithinDateRange($applicationEntry['transaction_date'])) {
                $ledgerEntries->push($applicationEntry);
            }
        }

        // 2. Add Loan Approval entry
        $approvalEntry = $this->createLoanApprovalEntry($loan, $runningBalances);

        if ($this->isWithinDateRange($approvalEntry['transaction_date'])) {
            $ledgerEntries->push($approvalEntry);
        }

        // 3. Add Disbursement entry
        if ($loan->disbursement) {
            $disbursementEntry = $this->createDisbursementEntry($loan, $runningBalances);

            if ($this->isWithinDateRange($disbursementEntry['transaction_date'])) {
                $ledgerEntries->push($disbursementEntry);
            }
        }

        // 4. Add Installment Due entries and Repayments in chronological order
        $this->addScheduleAndRepaymentEntries($loan, $ledgerEntries, $runningBalances);

        // Sort by transaction date
        $ledgerEntries = $ledgerEntries->sortBy('transaction_date')->values();

        if ($this->perPage > 0) {
            return $this->paginateCollection($ledgerEntries, $this->perPage);
        }

        return $ledgerEntries;
    }

    public function paginate($perPage = 100): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function filters(array $details): self
    {
        $this->startDate = Arr::get($details, 'startDate', '');
        $this->endDate = Arr::get($details, 'endDate', '');
        $this->loanId = Arr::get($details, 'loanId', 0);

        // Validate end date is not in the future
        if ($this->endDate && Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        return $this;
    }

    public function forLoan(int $loanId): self
    {
        $this->loanId = $loanId;
        return $this;
    }

    /**
     * Create loan application entry
     */
    protected function createLoanApplicationEntry(Loan $loan, array &$runningBalances): array
    {
        // Set initial balances based on loan terms
        $runningBalances['principal_balance'] = $loan->Credit_Amount;
        $runningBalances['interest_balance'] = $loan->totalInterest();
        $runningBalances['fees_balance'] = $loan->feesDue();
        $runningBalances['penalty_balance'] = 0;
        $runningBalances['total_balance'] = $runningBalances['principal_balance'] +
                                          $runningBalances['interest_balance'] +
                                          $runningBalances['fees_balance'];

        return [
            'loan_id' => $loan->id,
            'transaction_date' => $loan->loan_application->Credit_Application_Date->toDateTimeString(),
            'txn_id' => '',
            'type' => 'Loan Application',
            'principal' => $loan->Credit_Amount,
            'interest' => $loan->totalInterest(),
            'penalty' => 0,
            'fees' => 0,
            'total_paid' => 0,
            'balance_due' => 0,
            'principal_balance' => $runningBalances['principal_balance'],
            'interest_balance' => $runningBalances['interest_balance'],
            'fees_balance' => 0,
            'penalty_balance' => 0,
            'total_balance' => $runningBalances['total_balance'],
        ];
    }

    /**
     * Create loan approval entry
     */
    protected function createLoanApprovalEntry(Loan $loan, array &$runningBalances): array
    {
        $fees = $loan->totalFees();
        $runningBalances['fees_balance'] = $fees;
        $runningBalances['total_balance'] += $fees;

        return [
            'loan_id' => $loan->id,
            'transaction_date' => $loan->Credit_Account_Date->toDateTimeString(),
            'txn_id' => '',
            'type' => 'Loan Approval',
            'principal' => $loan->Credit_Amount,
            'interest' => $loan->totalInterest(),
            'penalty' => 0,
            'fees' => $fees,
            'total_paid' => 0,
            'balance_due' => 0,
            'principal_balance' => $runningBalances['principal_balance'],
            'interest_balance' => $runningBalances['interest_balance'],
            'fees_balance' => 0,
            'penalty_balance' => 0,
            'total_balance' => $runningBalances['total_balance'],
        ];
    }

    /**
     * Create disbursement entry
     */
    protected function createDisbursementEntry(Loan $loan, array &$runningBalances): array
    {
        $disbursement = $loan->disbursement;

        return [
            'loan_id' => $loan->id,
            'transaction_date' => $disbursement->disbursement_date->toDateTimeString(),
            'txn_id' => $this->generateTxnId($disbursement->id, 'DISB'),
            'type' => 'Disbursements',
            'principal' => $loan->Credit_Amount,
            'interest' => 0,
            'penalty' => 0,
            'fees' => 0,
            'total_paid' => 0,
            'balance_due' => 0,
            'principal_balance' => $runningBalances['principal_balance'],
            'interest_balance' => $runningBalances['interest_balance'],
            'fees_balance' => $runningBalances['fees_balance'],
            'penalty_balance' => 0,
            'total_balance' => $runningBalances['total_balance'],
        ];
    }

    /**
     * Add schedule and repayment entries in chronological order
     */
    protected function addScheduleAndRepaymentEntries(Loan $loan, Collection &$ledgerEntries, array &$runningBalances): void
    {
        // Get all schedule entries and repayments
        $scheduleEntries = $loan->schedule->sortBy('payment_due_date');
        $repayments = LoanRepayment::query()->where('Loan_ID', $loan->id)->orderBy('Transaction_Date')->get();

        // Merge and sort by date
        $allEntries = collect();

        // Add schedule entries
        foreach ($scheduleEntries as $schedule) {
            $allEntries->push([
                'type' => $schedule->type === 'Loan' ? 'schedule': 'fees',
                'date' => $schedule->payment_due_date->toDateTimeString(),
                'data' => $schedule
            ]);
        }

        $penalties = LoanPenalty::query()->where('Loan_ID', $loan->id)->orderBy('date')->get();

        foreach ($penalties as $penalty) {
            $allEntries->push([
                'type' => 'penalty',
                'date' => $penalty->date,
                'data' => $penalty
            ]);
        }

        // Add repayment entries
        foreach ($repayments as $repayment) {
            $allEntries->push([
                'type' => 'repayment',
                'date' => $repayment->Transaction_Date->toDateTimeString(),
                'data' => $repayment
            ]);
        }

        // Sort by date
        $allEntries = $allEntries->sortBy('date');

        // Process each entry
        foreach ($allEntries as $entry) {
            if ($entry['type'] === 'schedule') {
                $ledgerEntries->push($this->createScheduleEntry($entry['data'], $runningBalances));
            } else if ($entry['type'] === 'fees') {
                $ledgerEntries->push($this->createFeesEntry($entry['data'], $runningBalances));
            } else if ($entry['type'] === 'penalty') {
                $ledgerEntries->push($this->createPenaltyEntry($entry['data'], $runningBalances));
            } else if ($entry['type'] === 'repayment') {
                $repaymentEntry = $this->createRepaymentEntry($entry['data'], $runningBalances);
                if ($this->isWithinDateRange($repaymentEntry['transaction_date'])) {
                    $ledgerEntries->push($repaymentEntry);
                }
            }
        }
    }

    /**
     * Create schedule entry (Installment Due)
     */
    protected function createScheduleEntry(LoanSchedule $schedule, array &$runningBalances): array
    {
        return [
            'loan_id' => $schedule->loan_id,
            'transaction_date' => $schedule->payment_due_date->toDateTimeString(),
            'txn_id' => '',
            'type' => 'Installment Due',
            'principal' => $schedule->principal,
            'interest' => $schedule->interest,
            'penalty' => 0,
            'fees' => 0,
            'total_paid' => 0,
            'balance_due' => $schedule->total_outstanding,
            'principal_balance' => $runningBalances['principal_balance'],
            'interest_balance' => $runningBalances['interest_balance'],
            'fees_balance' => 0,
            'penalty_balance' => 0,
            'total_balance' => $runningBalances['total_balance'],
        ];
    }

    protected function createFeesEntry(LoanSchedule $schedule, array &$runningBalances): array
    {
        return [
            'loan_id' => $schedule->loan_id,
            'transaction_date' => $schedule->payment_due_date->toDateTimeString(),
            'txn_id' => '',
            'type' => $schedule->payable_to ? 'gnuGrid Fees Due': 'Fees Due',
            'principal' => 0,
            'interest' => 0,
            'penalty' => 0,
            'fees' => $schedule->total_payment,
            'total_paid' => 0,
            'balance_due' => $schedule->total_outstanding,
            'principal_balance' => $runningBalances['principal_balance'],
            'interest_balance' => $runningBalances['interest_balance'],
            'fees_balance' => 0,
            'penalty_balance' => 0,
            'total_balance' => $runningBalances['total_balance'],
        ];
    }

    protected function createPenaltyEntry(LoanPenalty $penalty, array &$runningBalances): array
    {
        $penaltyBalance = $penalty->Amount_To_Pay - $penalty->Amount;

        return [
            'loan_id' => $penalty->loan_id,
            'transaction_date' => $penalty->date,
            'txn_id' => '',
            'type' => 'Penalty',
            'principal' => 0,
            'interest' => 0,
            'penalty' => $penalty->Amount_To_Pay,
            'fees' => 0,
            'total_paid' => 0,
            'balance_due' => $penaltyBalance,
            'principal_balance' => $runningBalances['principal_balance'],
            'interest_balance' => $runningBalances['interest_balance'],
            'fees_balance' => 0,
            'penalty_balance' => $penaltyBalance,
            'total_balance' => $runningBalances['total_balance'] + $penaltyBalance,
        ];
    }

    /**
     * Create repayment entry
     */
    protected function createRepaymentEntry(LoanRepayment $repayment, array &$runningBalances): array
    {
        // Allocate payment (principal first, then interest, then fees, then penalties)
        $runningBalances['principal_balance'] -= $repayment->Principal;
        $runningBalances['interest_balance'] -= $repayment->Interest;
        $runningBalances['fees_balance'] -= $repayment->Fee;

        $runningBalances['penalty_balance'] = $repayment
            ->loan
            ->penalties()
            ->whereDate('date', '<=', Carbon::parse($repayment->Transaction_Date))
            ->sum('Amount_To_Pay');

        $runningBalances['penalty_balance'] -= $repayment->Penalty;
        $runningBalances['total_balance'] = $runningBalances['principal_balance'] +
                                          $runningBalances['interest_balance'] +
                                          $runningBalances['fees_balance'] +
                                          $runningBalances['penalty_balance'];

        return [
            'loan_id' => $repayment->Loan_ID,
            'transaction_date' => $repayment->Transaction_Date->toDateTimeString(),
            'txn_id' => $this->generateTxnId($repayment->id, 'REP'),
            'type' => 'Repayment',
            'principal' => $repayment->Principal,
            'interest' => $repayment->Interest,
            'penalty' => $repayment->Penalty,
            'fees' => $repayment->Fee,
            'total_paid' => round($repayment->amount, 2),
            'balance_due' => max(0, $runningBalances['total_balance']),
            'principal_balance' => $runningBalances['principal_balance'],
            'interest_balance' => $runningBalances['interest_balance'],
            'fees_balance' => max(0, $runningBalances['fees_balance']),
            'penalty_balance' => $runningBalances['penalty_balance'],
            'total_balance' => $runningBalances['total_balance'] == 0 ? 0 : $runningBalances['total_balance'],
        ];
    }

    /**
     * Generate transaction ID
     */
    protected function generateTxnId(int $id, string $prefix): string
    {
        return $prefix . str_pad($id, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Check if date is within the specified range
     */
    protected function isWithinDateRange($date): bool
    {
        if (!$this->startDate || !$this->endDate) {
            return true;
        }

        $checkDate = Carbon::parse($date);
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();

        return $checkDate->between($startDate, $endDate);
    }

    /**
     * Paginate a collection
     */
    protected function paginateCollection(Collection $collection, int $perPage): LengthAwarePaginator
    {
        $currentPage = Paginator::resolveCurrentPage();
        $currentItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );
    }
}
