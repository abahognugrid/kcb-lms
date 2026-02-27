<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsUssdSessionTracking extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected function casts() : array
    {
        return [
            'Maturity_Date' => 'date',
        ];
    }

    public function loanProduct()
    {
        return $this->belongsTo(LoanProduct::class, "Loan_Producd_Code", "Code");
    }

    public function loanProductTerm()
    {
        return $this->belongsTo(LoanProductTerm::class, "Loan_Producd_Term_Code", "Code");
    }

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class, "Loan_Application_ID", "id");
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, "Loan_ID", "id");
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, "Customer_Phone_Number", "Telephone_Number");
    }
}
