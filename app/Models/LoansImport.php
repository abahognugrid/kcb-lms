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
        'id_type',
        'id_number',
        'maturity_date',
        'loan_amount',
        'outstanding_amount',
    ];
}
