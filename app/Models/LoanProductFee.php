<?php

namespace App\Models;

use App\Models\Accounts\Account;
use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class LoanProductFee extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $fillable = [
        'Name',
        'partner_id',
        'Calculation_Method',
        'Value',
        'Tiers',
        'Applicable_On',
        'Loan_Product_ID',
        'Applicable_At',
        'Description',
        'Payable_Account_ID',
        'Charge_Interest',
        'is_part_of_interest',
    ];

    public const CALCULATION_METHODS = [
        'Flat',
        'Percentage',
        'Tiered',
    ];

    public const APPLICABLE_ON_OPTIONS = [
        'None',
        'Principal',
        'Interest',
        'Balance',
    ];

    public const APPLICABLE_AT_OPTIONS = [
        'Disbursement',
        'Repayment',
        'Application',
        'Maturity', // Added Maturity as an option
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }
    public function loanProduct()
    {
        return $this->belongsTo(LoanProduct::class, 'Loan_Product_ID', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'Payable_Account_ID');
    }

    protected function casts(): array
    {
        return [
            'Tiers' => 'array',
        ];
    }
}
