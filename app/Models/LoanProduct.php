<?php

namespace App\Models;

use App\Enums\LoanAccountType;
use App\Models\Accounts\Account;
use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Accounts\Contracts\Accountable;
use App\Services\Account\AccountSeederService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanProduct extends Model implements Accountable, Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $fillable = [
        'Name',
        'partner_id',
        'Term',
        'Interest_Rate',
        'Interest_Calculation_Method',
        'Loan_Product_Type_ID',
        'Applicable_On',
        'Has_Advance_Payment',
        'Advance_Calculation_Method',
        'Advance_Value',
        'Repayment_Cycles',
        'Minimum_Principal_Amount',
        'Default_Principal_Amount',
        'Maximum_Principal_Amount',
        'Repayments_Rounding_Method',
        'Round_All_Repayments',
        'Repayment_Order',
        'Extend_Loan_After_Maturity',
        'Interest_Type_After_Maturity',
        'Interest_Value_After_Maturity',
        'Interest_After_Maturity_Calculation_Method',
        'Include_Fees_After_Maturity',
        'Recurring_Period_After_Maturity_Type',
        'Recurring_Period_After_Maturity_Value',
        'Decimal_Place',
        'Round_UP_or_Off_all_Interest',
        'Code',
        'Payable_Account_ID',
        'Loss_Provision_Account_ID',
        'Written_Off_Expense_Account_ID',
        'Enrollment_Type',
        'Auto_Debit',
        'Switch_ID',
        'Arrears_Auto_Write_Off_Days',
        'can_write_off_interest',
        'can_write_off_penalties',
        'can_write_off_fees',
        'Ussd_Code',
        'Allows_Multiple_Loans',
        'Allows_Users_With_Loans_From_Other_Partners'
    ];

    protected function casts()
    {
        return [
            'Repayment_Order' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
        self::created(function (LoanProduct $loan_product) {
            AccountSeederService::addToFixedAccount($loan_product);
        });
    }

    public function loan_product_type()
    {
        return $this->belongsTo(LoanProductType::class, 'Loan_Product_Type_ID', 'id');
    }

    public function type()
    {
        // TODO Raymond: Remove this duplicate r/ship?
        return $this->belongsTo(LoanProductType::class, 'Loan_Product_Type_ID', 'id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'Loan_Product_ID', 'id');
    }

    public function portfolio()
    {
        return $this->through('loans')
            ->has('loan_schedules')
            ->whereIn('loans.Credit_Account_Status', [LoanAccountType::WithinTerms, LoanAccountType::BeyondTerms]);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    public function general_ledger_account(): MorphOne
    {
        return $this->morphOne(Account::class, 'accountable');
    }

    public function fees(): HasMany
    {
        return $this->hasMany(LoanProductFee::class, 'Loan_Product_ID', 'id');
    }

    public function loan_product_fees(): HasMany
    {
        return $this->hasMany(LoanProductFee::class, 'Loan_Product_ID', 'id');
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(LoanProductPenalties::class, 'Loan_Product_ID', 'id');
    }

    public function loan_product_penalties(): HasMany
    {
        return $this->hasMany(LoanProductPenalties::class, 'Loan_Product_ID', 'id');
    }

    public function switch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Switches::class, 'Switch_ID', 'id')
            ->where('category', 'Payment')
            ->where('status', 'On');
    }

    public function accountDisplayName(): string
    {
        return $this->Name;
    }

    public function fixedParentSlug(): string
    {
        return AccountSeederService::LOAN_PRODUCTS_FIXED_SLUG;
    }

    public function getIndentifier(): string
    {
        return "ALP." . $this->id;
    }

    public function getTypeLetter(): string
    {
        return "A";
    }

    public function loan_product_terms()
    {
        return $this->hasMany(LoanProductTerm::class, 'Loan_Product_ID', 'id');
    }

    public function sms_templates(): HasMany
    {
        return $this->hasMany(SmsTemplate::class, 'Loan_Product_ID');
    }

    public function smsTemplates(): HasMany
    {
        return $this->hasMany(SmsTemplate::class);
    }

    public function payable_account()
    {
        return $this->hasOne(Account::class, 'id', 'Payable_Account_ID');
    }

    public function portfolioSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->portfolio()->sum('principal_remaining');
            }
        );
    }

    public function lossProvisionAccount()
    {
        return Account::query()
            ->where('slug', AccountSeederService::LOAN_LOSS_PROVISION_SLUG)
            ->where('partner_id', $this->partner_id)
            ->first();
    }

    public function provisionForBadDebtsAccount()
    {
        return Account::query()
            ->where('slug', AccountSeederService::PROVISION_FOR_BAD_DEBT_SLUG)
            ->where('partner_id', $this->partner_id)
            ->first();
    }

    public function loanLossProvisions(): HasMany
    {
        return $this->hasMany(LoanLossProvision::class);
    }

    public function ussdCode()
    {
        return ! empty($this->Ussd_Code) ? $this->Ussd_Code : config('lms.ussd_code');
    }
}
