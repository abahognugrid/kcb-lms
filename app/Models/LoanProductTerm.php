<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditingAuditable;

class LoanProductTerm extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditingAuditable;

    protected $fillable = [
        'partner_id',
        'Loan_Product_ID',
        'Interest_Rate',
        'Interest_Calculation_Method',
        'Value',
        'Has_Advance_Payment',
        'Advance_Calculation_Method',
        'Advance_Value',
        'Repayment_Cycles',
        'Extend_Loan_After_Maturity',
        'Interest_Type_After_Maturity',
        'Interest_Value_After_Maturity',
        'Interest_After_Maturity_Calculation_Method',
        'Recurring_Period_After_Maturity_Type',
        'Recurring_Period_After_Maturity_Value',
        'Include_Fees_After_Maturity',
        'is_active',
        'Code',
        'Write_Off_After_Days',
        'Interest_Cycle',
        'Interest_Charged_At',
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($term) {
            $id = strtoupper(uniqid());
            $term->Code = "LPT{$term->Loan_Product_ID}-{$id}";
        });
    }

    public function loan_product()
    {
        return $this->belongsTo(LoanProduct::class, 'Loan_Product_ID');
    }
}
