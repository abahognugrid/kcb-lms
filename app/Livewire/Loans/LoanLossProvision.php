<?php

namespace App\Livewire\Loans;

use App\Enums\LoanAccountType;
use App\Models\JournalEntry;
use App\Models\LoanLossProvision as LoanLossProvisionModel;
use App\Models\LoanProduct;
use App\Models\LoanSchedule;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanLossProvision extends Component
{
    public ?int $loanProductId = null;
    public ?string $ageing_category = '';
    public int $minimum_days = 0;
    public int $maximum_days = 0;
    public float $provision_rate = 0.0;
    public int $provision_amount = 0;
    public int $arrears_amount = 0;
    public ?int $provisionId = null;
    public bool $approvedRequested = false;
    public bool $provisionConfirmed = false;
    public bool $editingProvision = false;
    public int $batch_number = 0;

    protected array $rules = [
        'ageing_category' => [
            'required',
            'string',
            'max:25',
        ],
        'minimum_days' => 'required|integer|min:0',
        'maximum_days' => 'required|integer|min:0',
        'provision_rate' => 'required|numeric|gte:0|max:100',
    ];

    public function mount($loanProductId)
    {
        $this->loanProductId = $loanProductId;

        $provision = LoanLossProvisionModel::query()
            ->with('approvedBy')
            ->where('loan_product_id', $this->loanProductId)
            ->whereNotNull('approved_at')
            ->orderBy('id', 'desc')
            ->first();

        $this->batch_number = ($provision ? $provision->batch_number : 0) + 1;
    }

    public function render()
    {
        $latest_provision = LoanLossProvisionModel::query()
            ->where('loan_product_id', $this->loanProductId)
            ->orderBy('id', 'desc')
            ->first();

        $provisions = LoanLossProvisionModel::query()
            ->with('approvedBy')
            ->where('loan_product_id', $this->loanProductId)
            ->when(!empty($latest_provision), function ($q) use ($latest_provision) {
                $q->where('batch_number', $latest_provision->batch_number);
            })
            ->orderBy('minimum_days')
            ->get();
        return view('livewire.loans.loan-loss-provision', [
            'provisions' => $provisions,
            'maximizedProvision' => $provisions->count() === 5 || $provisions->filter(fn($provision) => $provision->maximum_days === 0)->isNotEmpty(),
            'requiresApproval' => $provisions->filter(fn($provision) => empty($provision->approved_at))->isNotEmpty(),
        ]);
    }

    public function updatedMinimumDays($value)
    {
        $this->addProvisionAmounts();
    }

    public function updatedMaximumDays($value)
    {
        $this->addProvisionAmounts();
    }

    public function updatedProvisionRate($value)
    {
        if ($value == 0) {
            return;
        }

        $this->provision_amount = round($this->arrears_amount * ($value / 100));
    }

    public function addProvision(): void
    {
        $record = $this->validate($this->rules());
        $record['partner_id'] = 1;
        $record['created_by'] = auth()->user()->id;
        $record['loan_product_id'] = $this->loanProductId;
        $record['provision_amount'] = 0;
        $record['arrears_amount'] = 0;
        $record['batch_number'] = $this->batch_number;

        LoanLossProvisionModel::query()->create($this->addProvisionAmounts($record));

        $this->reset(['ageing_category', 'minimum_days', 'maximum_days', 'provision_rate', 'provision_amount', 'arrears_amount']);
    }

    public function addBatchProvision(): void
    {
        $provisions = LoanLossProvisionModel::query()
            ->with('approvedBy')
            ->where('loan_product_id', $this->loanProductId)
            ->where('batch_number', ($this->batch_number - 1))
            ->whereNotNull('approved_at')
            ->orderBy('minimum_days')
            ->get();

        try {
            DB::transaction(function () use ($provisions) {
                $provisions->each(function ($provision) {
                    $newProvision = $provision->replicate();
                    $newProvision->batch_number = $this->batch_number;
                    $newProvision->approved_at = null;
                    $newProvision->approved_by = null;
                    $newProvision->save();
                });
            });

            $this->dispatch('$refresh');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            throw new Exception('Internal error occured while creating the new batch');
        }
    }

    protected function rules(): array
    {
        $rules = $this->rules;
        $rules['ageing_category'][] = Rule::unique('loan_loss_provisions')->where(function ($query) {
            return $query->where('loan_product_id', $this->loanProductId)->where('partner_id', 1);
        });

        return $rules;
    }

    public function editProvision(LoanLossProvisionModel $provision): void
    {
        $this->minimum_days = $provision->minimum_days;
        $this->maximum_days = $provision->maximum_days;
        $this->ageing_category = $provision->ageing_category;
        $this->provision_rate = $provision->provision_rate;
        $this->provision_amount = $provision->provision_amount;
        $this->arrears_amount = $provision->arrears_amount;
        $this->provisionId = $provision->id;
        $this->editingProvision = true;
    }

    public function updateProvision(?LoanLossProvisionModel $provision = null): void
    {
        if (empty($provision)) {
            return;
        }

        $record = [
            'ageing_category' => $this->ageing_category,
            'minimum_days' => $this->minimum_days,
            'maximum_days' => $this->maximum_days,
            'provision_rate' => $this->provision_rate,
            'provision_amount' => 0,
            'arrears_amount' => 0,
        ];

        $record = $this->addProvisionAmounts($record);

        $differenceWithExistingProvisionAmount = $record['provision_amount'] - $provision->provision_amount;

        $provision->ageing_category = $this->ageing_category;
        $provision->fill($record)->save();

        // If the provision was already approved, then we need to update journal entries as well.

        $this->reset(['ageing_category', 'minimum_days', 'maximum_days', 'provision_rate', 'provision_amount', 'arrears_amount', 'editingProvision', 'provisionId']);

        if (empty($provision->approved_at) || $differenceWithExistingProvisionAmount === 0) {
            /**
             * This provision has not yet been approved, so we allow the user to make changes as they wish
             * Or there was no change in the provisioned amount, so there is no need to post any journal entries.
             */
            return;
        }

        if ($differenceWithExistingProvisionAmount > 0) {
            /**
             * The provision amount has increased so we need to post the increment to the journal entries
             */
            $this->postProvisionJournalEntries($provision, $differenceWithExistingProvisionAmount);
            return;
        }

        /**
         * The provision amount has decreased so we need to make an adjustment to the journal entries
         * i.e. DR: Provision and CR: Bad Debts with the difference.
         */

        $this->adjustProvisionJournalEntries($provision, abs($differenceWithExistingProvisionAmount));
    }

    public function approveProvision()
    {
        $this->createLoanLossProvision();

        session()->flash('success', 'Loan provision approved successfully.');

        return redirect()
            ->route('loan-products.show', $this->loanProductId);
    }

    public function deleteProvision(LoanLossProvisionModel $provision): void
    {
        $provision->delete();
    }

    /**
     * @return LoanSchedule|mixed|null
     */
    public function getArrears(): mixed
    {
        $query = LoanSchedule::query()
            ->whereDate('payment_due_date', '<', now())
            ->whereRelation('loan', function ($query) {
                $query->where('partner_id', 1)
                    ->where('Loan_Product_ID', $this->loanProductId)
                    ->whereNotIn('Credit_Account_Status', [
                        LoanAccountType::WrittenOff,
                        LoanAccountType::PaidOff
                    ]);
            })
            ->selectRaw('COALESCE(SUM(principal_remaining), 0) as arrears_amount, COALESCE(SUM(interest_remaining), 0) as suspended_interest')
            ->where('principal_remaining', '>', 0);

        if ($this->maximum_days === 0) {
            $query->whereDate('payment_due_date', '<=', now()->subDays($this->minimum_days)->toDateString());
        } else {
            $query
                ->whereBetween('payment_due_date', [
                    now()->subDays($this->maximum_days)->format('Y-m-d'),
                    now()->subDays($this->minimum_days)->toDateString()
                ]);
        }

        return $query->first();
    }

    /**
     * @param array $record
     * @return array
     */
    public function addProvisionAmounts(array $record = []): array
    {
        $arrearsResult = $this->getArrears();
        $arrearsAmount = +$arrearsResult->arrears_amount;
        $suspendedInterest = +$arrearsResult->suspended_interest;

        if ($arrearsAmount == 0) {
            return $record;
        }

        $this->arrears_amount = $arrearsAmount;
        $record['arrears_amount'] = $arrearsAmount;
        $record['suspended_interest'] = $suspendedInterest;
        $record['provision_amount'] = round($arrearsAmount * ($this->provision_rate / 100));
        $this->provision_amount = $record['provision_amount'];
        return $record;
    }

    private function createLoanLossProvision(): void
    {
        DB::transaction(function () {
            $provisions = LoanLossProvisionModel::query()
                ->where('loan_product_id', $this->loanProductId)
                ->where('batch_number', $this->batch_number)
                ->get();

            LoanLossProvisionModel::query()
                ->where('loan_product_id', $this->loanProductId)
                ->where('batch_number', $this->batch_number)
                ->update([
                    'approved_at' => now(),
                    'approved_by' => auth()->id()
                ]);

            $provisions->each(function (LoanLossProvisionModel $provision) {
                $this->postProvisionJournalEntries($provision);
            });
        });
    }

    private function postProvisionJournalEntries(LoanLossProvisionModel $provision, int $amount = 0): void
    {
        $amount = $amount === 0 ? $provision->provision_amount : $amount;

        if (! $amount > 0) {
            return;
        }

        $loanProduct = LoanProduct::query()->findOrFail($provision->loan_product_id);
        $lossProvisionAccount = $loanProduct->lossProvisionAccount();
        $provisionForBadDebtsAccount = $loanProduct->provisionForBadDebtsAccount();

        $now = now();
        $journalEntries = [];
        $txn_id = rand(11111, 99999) . "-" . $now->unix();

        // Debit Record
        $currentBalance = $provisionForBadDebtsAccount->balance + $amount;
        $journalEntries[] = [
            'account_id' => $provisionForBadDebtsAccount->id,
            'amount' => $amount,
            'transactable_id' => $provision->id,
            'transactable' => LoanLossProvisionModel::class,
            'partner_id' => $provision->partner_id,
            'txn_id' => $txn_id,
            'account_name' => $provisionForBadDebtsAccount->name,
            'cash_type' => 'Non Cash',
            'previous_balance' => $provisionForBadDebtsAccount->balance,
            'current_balance' => $currentBalance,
            'accounting_type' => 'Debit',
            'credit_amount' => 0,
            'debit_amount' => $amount,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $provisionForBadDebtsAccount->balance = $currentBalance;
        $provisionForBadDebtsAccount->save();

        $currentBalance = $lossProvisionAccount->balance + $amount;
        // Credit Record
        $journalEntries[] = [
            'account_id' => $lossProvisionAccount->id,
            'amount' => $amount,
            'transactable_id' => $provision->id,
            'transactable' => LoanLossProvisionModel::class,
            'partner_id' => $provision->partner_id,
            'txn_id' => $txn_id,
            'account_name' => $lossProvisionAccount->name,
            'cash_type' => 'Non Cash',
            'previous_balance' => $lossProvisionAccount->balance,
            'current_balance' => $currentBalance,
            'accounting_type' => 'Credit',
            'credit_amount' => $amount,
            'debit_amount' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $lossProvisionAccount->balance = $currentBalance;
        $lossProvisionAccount->save();

        JournalEntry::query()->insert($journalEntries);
    }

    private function adjustProvisionJournalEntries(LoanLossProvisionModel $provision, int $amount = 0): void
    {
        $amount = $amount === 0 ? $provision->provision_amount : $amount;

        if (! $amount > 0) {
            return;
        }

        $loanProduct = LoanProduct::query()->findOrFail($provision->loan_product_id);
        $lossProvisionAccount = $loanProduct->lossProvisionAccount();
        $provisionForBadDebtsAccount = $loanProduct->provisionForBadDebtsAccount();

        $now = now();
        $journalEntries = [];
        $txn_id = rand(11111, 99999) . "-" . $now->unix();

        // Debit Record
        $currentBalance = $lossProvisionAccount->balance - $amount;
        $journalEntries[] = [
            'account_id' => $lossProvisionAccount->id,
            'amount' => $amount,
            'transactable_id' => $provision->id,
            'transactable' => LoanLossProvisionModel::class,
            'partner_id' => $provision->partner_id,
            'txn_id' => $txn_id,
            'account_name' => $lossProvisionAccount->name,
            'cash_type' => 'Non Cash',
            'previous_balance' => $lossProvisionAccount->balance,
            'current_balance' => $currentBalance,
            'accounting_type' => 'Debit',
            'credit_amount' => 0,
            'debit_amount' => $amount,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $lossProvisionAccount->balance = $currentBalance;
        $lossProvisionAccount->save();

        // Credit Record
        $currentBalance = $provisionForBadDebtsAccount->balance - $amount;
        $journalEntries[] = [
            'account_id' => $provisionForBadDebtsAccount->id,
            'amount' => $amount,
            'transactable_id' => $provision->id,
            'transactable' => LoanLossProvisionModel::class,
            'partner_id' => $provision->partner_id,
            'txn_id' => $txn_id,
            'account_name' => $provisionForBadDebtsAccount->name,
            'cash_type' => 'Non Cash',
            'previous_balance' => $provisionForBadDebtsAccount->balance,
            'current_balance' => $currentBalance,
            'accounting_type' => 'Credit',
            'credit_amount' => $amount,
            'debit_amount' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $provisionForBadDebtsAccount->balance = $currentBalance;
        $provisionForBadDebtsAccount->save();

        JournalEntry::query()->insert($journalEntries);
    }
}
