<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoansImport extends Model
{
    use HasFactory;

    protected $table = 'loans_import';

    protected $fillable = [
        'telephone_number',
        'first_name',
        'last_name',
        'other_name',
        'gender',
        'date_of_birth',
        'id_type',
        'id_number',
        'loan_application_date',
        'maturity_date',
        'loan_amount',
        'amount_paid',
        'loan_penalty',
        'outstanding_amount',
        'loan_status',
    ];
}
