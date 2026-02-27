<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class LoanProductPenalties extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $guarded = [];

    const CALCULATION_METHODS = [
        'Flat',
        'Percentage',
    ];

    const PENALTY_APPLICATION_FORMS = [
        'Overdue Principal',
        'Overdue Interest',
        'Overdue Principal And Interest',
        'Total Outstanding Balance',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }
    public function loanProduct()
    {
        return $this->belongsTo(LoanProduct::class, 'Loan_Product_ID', 'id');
    }
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }
}
