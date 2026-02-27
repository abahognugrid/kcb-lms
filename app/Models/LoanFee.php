<?php

namespace App\Models;

use App\Models\Loan;
use App\Models\Customer;
use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanFee extends Model
{
    use HasFactory, SoftDeletes;

    const FULLY_PAID = 'Fully Paid';
    const NOT_PAID = 'Not Paid';
    const PARTIALLY_PAID = 'Partially Paid';

    protected $fillable = [
        "partner_id",
        "Loan_ID",
        "Loan_Product_Fee_ID",
        "Customer_ID",
        "Amount",
        "Amount_To_Pay",
        "Charge_At",
        "Payable_Account_ID",
        "Status",
        "is_part_of_interest"
    ];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, "Customer_ID");
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, "Loan_ID");
    }

    public function loan_product_fee()
    {
        return $this->belongsTo(LoanProductFee::class, "Loan_Product_Fee_ID");
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, "partner_id");
    }

    public function amount()
    {
        return $this->Amount;
    }
}
