<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditScore extends Model
{
    use HasFactory;

    protected $table = 'credit_scores';

    protected $casts = [
        'mnoPhoneNumbers' => 'array'
    ];

    protected $fillable = [
        'customerId',
        'mnoName',
        'mnoGender',
        'mnoDateOfBirth',
        'crbName',
        'crbGender',
        'crbDateOfBirth',
        'altName',
        'altGender',
        'altDateOfBirth',
        'mnoPhoneNumbers',
        'mnoPeriod',
        'mnoScore',
        'mnoBand',
        'mnoRating',
        'mnoProbabilityOfDefaultPercent',
        'mnoLikelihoodToDefault',
        'mnoPreviousScore',
        'mnoMonthsActive',
        'mnoAccounts12Months',
        'mnoTotalLoanAmount12Months',
        'mnoClosedAccounts12Months',
        'mnoTotalPayment12Months',
        'mnoTotalTurnoverCount6Months',
        'mnoMonthlyTurnoverAmount6Months',
        'mnoTotalSpendCount6Months',
        'mnoMonthlySpendAmount6Months',
        'mnoTotalTurnoverCount3Months',
        'mnoMonthlyTurnoverAmount3Months',
        'mnoTotalSpendCount3Months',
        'mnoMonthlySpendAmount3Months',
        'crbStatus',
        'crbDisputed',
        'crbPeriod',
        'crbScore',
        'crbBand',
        'crbRating',
        'crbProbabilityOfDefaultPercent',
        'crbLikelihoodToDefault',
        'crbTotalAccounts',
        'crbTotalAccounts12Months',
        'crbOpenAccounts',
        'crbOpenAccounts12Months',
        'crbClosedAccounts',
        'crbClosedAccounts12Months',
        'crbAdverseAccounts',
        'crbAdverseAccounts12Months',
        'crbWorstActiveDaysInArrears',
        'crbWorstDaysInArrears',
        'crbWorstDaysInArrears12Months',
        'altStatus',
        'altDisputed',
        'altPeriod',
        'altScore',
        'altBand',
        'altRating',
        'altProbabilityOfDefaultPercent',
        'altLikelihoodToDefault',
        'altTotalAccounts',
        'altTotalAccounts12Months',
        'altOpenAccounts',
        'altOpenAccounts12Months',
        'altClosedAccounts',
        'altClosedAccounts12Months',
        'altAdverseAccounts',
        'altAdverseAccounts12Months',
        'altWorstActiveDaysInArrears',
        'altWorstDaysInArrears',
        'altWorstDaysInArrears12Months',
    ];
}
