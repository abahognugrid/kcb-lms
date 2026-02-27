<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class CreditLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'partner_id',
        'credit_limit',
        'used_credit',
        'available_credit',
    ];

    /**
     * Get the customer associated with this credit limit.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function totalLoanAmount()
    {
        return Loan::where('Customer_ID', $this->customer_id)
            ->sum(DB::raw('"Credit_Amount"::DECIMAL'));
    }

    public function totalLoanCount()
    {
        return Loan::where('Customer_ID', $this->customer_id)->count();
    }

    public function totalOutstandingBalance()
    {
        $loans = Loan::where('Customer_ID', $this->customer_id);
        return $loans->get()->sum(function ($loan) {
            return $loan->totalOutstandingBalance();
        });
    }

    /**
     * Get the partner that issued the credit.
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Calculate remaining credit dynamically (if needed).
     */
    public function getRemainingCreditAttribute()
    {
        return $this->credit_limit - $this->used_credit;
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }
}
