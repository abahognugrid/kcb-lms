<?php

namespace App\Models;

use App\Traits\HasStatuses;
use App\Models\Scopes\PartnerScope;
use App\Services\LoanService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Log;

class LoanApplication extends Model
{
    use HasFactory, SoftDeletes, HasStatuses;

    protected $fillable = [
        'Request_ID',
        'partner_id',
        'Customer_ID',
        'Loan_Product_ID',
        'Credit_Score_ID',
        'Loan_Purpose',
        'Applicant_Classification',
        'Credit_Application_Date',
        'Amount',
        'Credit_Application_Status',
        'Credit_Account_or_Loan_Product_Type',
        'Credit_Application_Duration',
        'Client_Consent_flag',
        'Country',
        'District',
        'Subcounty',
        'Parish',
        'Village',
        'Last_Status_Change_Date',
        'Credit_Amount_Approved',
        'PCI_Flag_of_Ownership',
        'PCI_Flag_of_Ownership',
        'PCI_Country_Code',
        'Disbursement_Documents_Uploaded',
        'Rejection_Reason',
        'Rejection_Reference',
        'Rejection_Date',
        'Approval_Reference',
        'Approval_Narration',
        'Approval_Date',
        'Approved_By',
        'Partner_Application_Number',
    ];

    protected function casts(): array
    {
        return [
            'Credit_Application_Date' => 'datetime',
            'Last_Status_Change_Date' => 'datetime',
            'Rejection_Date' => 'datetime',
            'Approval_Date' => 'datetime',
            'Disbursement_Documents_Uploaded' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public static function rules($customer = null)
    {
        $customer_email_rule = [];

        if ($customer) {
            $customer_email_rule = ['email_address' => 'nullable|string|max:100|unique:customers,Email_Address,' . $customer->id];
        } else {
            $customer_email_rule = ['email_address' => 'required|string|max:100|unique:customers,Email_Address'];
        }

        return [
            "first_name" => "required|string|max:100",
            "last_name" => "required|string|max:100",
            "other_name" => "nullable|string|max:100",
            "gender" => "required|string|max:100|in:Male,Female",
            "marital_status" => "sometimes|string|max:100|in:Single (never married),Married,Divorced,Widowed,Separated,Annulled,Cohabitating,Other",
            "date_of_birth" => "required|date",
            "id_type" => "required|string", // TODO: Add validation
            "id_number" => "required|string|max:100",
            "classification" => "required|string|max:100|in:Individual,Non-Indvidual",
            "telephone_number" => "required|string|max:15"
        ] + $customer_email_rule;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Customer_ID');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function creditScore()
    {
        return $this->belongsTo(CreditScore::class, 'Credit_Score_ID');
    }

    public function loan_product()
    {
        return $this->belongsTo(LoanProduct::class, 'Loan_Product_ID');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'Loan_Application_ID');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'Loan_Application_ID');
    }

    /**
     * Use when you know the application will always have just on transaction.
     * Otherwise, use transactions() method instead.
     */
    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'Loan_Application_ID');
    }

    public function loan_session()
    {
        return $this->hasOne(LmsUssdSessionTracking::class, 'Loan_Application_ID');
    }

    public function notApproved()
    {
        return empty($this->loan_id);
    }

    public function generateLoanSummaryDetails(Customer $customer, $loan_amount, $loan_purpose, $loan_product_id, $application_date)
    {

        try {
            $loanAmount = $loan_amount;
            $frequencyOfInstallmentRepayment = "Monthly";
            $loanProductTerm = LoanProductTerm::where('Loan_Product_ID', $loan_product_id)->first();

            $loanProduct = LoanProduct::where('id', $loan_product_id)->first();

            $totalRepaymentAmount = 0;
            $dailyRepaymentAmount = 0;

            $numberOfPayments = Loan::determineNumberOfPayments($loanTermInDays ?? $loanProductTerm->Value, $frequencyOfInstallmentRepayment);
            if ($loanProductTerm->Interest_Calculation_Method == "Flat") {
                $totalScheduleAmounts = LoanSchedule::calculateFlatTotalRepaymentAndInterest(
                    $loanAmount,
                    $loanProductTerm->Interest_Rate,
                    $loanTermInDays ?? $loanProductTerm->Value,
                    $frequencyOfInstallmentRepayment,
                );
                $totalRepaymentAmount = $totalScheduleAmounts['total_repayment'];
                $dailyRepaymentAmount = $totalScheduleAmounts['daily_repayment'];
            } else if ($loanProductTerm->Interest_Calculation_Method == "Declining Balance - Discounted") {
                $totalScheduleAmounts = LoanSchedule::calculateDecliningBalanceTotalRepaymentAndInterest(
                    $loanAmount,
                    $loanProductTerm->Interest_Rate,
                    $numberOfPayments,
                    $frequencyOfInstallmentRepayment,
                    $loanProductTerm->Interest_Cycle,
                );
                $totalRepaymentAmount = $totalScheduleAmounts['total_repayment'];
                $dailyRepaymentAmount = $totalScheduleAmounts['daily_repayment'];
            } else if ($loanProductTerm->Interest_Calculation_Method == "Amortization") {
                $totalScheduleAmounts = LoanSchedule::calculateAmortizedTotalRepaymentAndInterest(
                    $loanAmount,
                    $loanProductTerm->Interest_Rate,
                    $numberOfPayments,
                    $frequencyOfInstallmentRepayment,
                    $loanProductTerm->Interest_Cycle,
                );
                $totalRepaymentAmount = $totalScheduleAmounts['total_repayment'];
                $dailyRepaymentAmount = $totalScheduleAmounts['daily_repayment'];
            } else if ($loanProductTerm->Interest_Calculation_Method == "Flat on Loan Amount") {
                $totalScheduleAmounts = LoanSchedule::calculateFlatAmountTotalRepaymentAndInterest(
                    $loanAmount,
                    $loanProductTerm->Interest_Rate,
                    $loanTermInDays ?? $loanProductTerm->Value,
                    $frequencyOfInstallmentRepayment,
                );
                $totalRepaymentAmount = $totalScheduleAmounts['total_repayment'];
                $dailyRepaymentAmount = $totalScheduleAmounts['daily_repayment'];
            }
            $credit_account_type = $loanProduct->loan_product_type; // asset or mobile loan

            $loanRecordDetails = [
                'Loan_Product_ID' => $loan_product_id,
                'Loan_Purpose' => $loan_purpose,
                'Applicant_Classification' => $customer->Classification,
                'Credit_Application_Date' => $application_date,
                'Amount' => $loanAmount,
                'Credit_Application_Status' => 'Pending',
                'Credit_Account_or_Loan_Product_Type' => $credit_account_type->Code,
                'Credit_Application_Duration' => '0', // time between application and the time it is approved or rejected. This is auto so zero(0)
                'Client_Consent_flag' => 'Yes',
            ];

            $fees = $loanProduct->fees;

            $feeAmount = 0;
            $feesStructure = [];
            $collectionFees = [];
            $totalOtherFees = 0;

            // First Pass: Process all fees except those applicable on installments
            foreach ($fees as $fee) {

                $Value = $fee->Value;
                $Calculation_Method = $fee->Calculation_Method;
                $Applicable_On = $fee->Applicable_On;

                if ($Calculation_Method == "Percentage") {
                    if ($Applicable_On == "Principal") {
                        $principal = $loanAmount;
                        $feeAmount = $principal * $Value / 100;
                        $feesStructure[] = ['name' => $fee->Name, 'amount' => 'UGX ' . number_format($feeAmount), 'charged_at' => $fee->Applicable_At];
                        $totalOtherFees += $feeAmount;
                        $totalRepaymentAmount += ($totalOtherFees * $numberOfPayments);
                    } else if ($Applicable_On == "Interest") {
                        $interest = $loanProductTerm->Interest_Rate;
                        $feeAmount = $interest * $Value / 100;
                        $feesStructure[] = ['name' => $fee->Name, 'amount' => 'UGX ' . number_format($feeAmount), 'charged_at' => $fee->Applicable_At];
                        $totalOtherFees += $feeAmount;
                        $totalRepaymentAmount += ($totalOtherFees * $numberOfPayments);
                    } else if ($Applicable_On == "Balance") {
                        $principal = $loanAmount;
                        $interest = $loanProductTerm->Interest_Rate;
                        $balance = $principal + $interest;
                        $feeAmount = $balance * $Value / 100;
                        $feesStructure[] = ['name' => $fee->Name, 'amount' => 'UGX ' . number_format($feeAmount), 'charged_at' => $fee->Applicable_At];
                        $totalOtherFees += $feeAmount;
                        $totalRepaymentAmount += ($totalOtherFees * $numberOfPayments);
                    }
                } else {
                    $feeAmount = $fee->Value;
                    $feesStructure[] = ['name' => $fee->Name, 'amount' => 'UGX ' . number_format($feeAmount), 'charged_at' => $fee->Applicable_At];
                    $totalOtherFees += $feeAmount;
                    $totalRepaymentAmount += ($totalOtherFees * $numberOfPayments);
                }
                // Store installment fees separately for the second pass
                if ($Applicable_On == 'Installment Balance') {
                    $collectionFees[] = $fee;
                }
            }

            //Second Pass: Apply installment fees as a percentage of all other fees (excluding application fees)
            foreach ($collectionFees as $fee) {
                $Value = $fee->Value;
                $feeAmount = $dailyRepaymentAmount * ($Value / 100);
                $dailyRepaymentAmount = $dailyRepaymentAmount + $totalOtherFees + $feeAmount;
                $feesStructure[] = ['name' => $fee->Name, 'amount' => 'UGX ' . number_format($feeAmount), 'charged_at' => $fee->Applicable_At];
                $totalRepaymentAmount += ($feeAmount * $numberOfPayments);
            }

            $totalFacilitationFees = array_sum(array_map(function ($fee) {
                return (int) filter_var($fee['amount'], FILTER_SANITIZE_NUMBER_INT);
            }, $feesStructure));

            $totalFacilitationFees = $totalFacilitationFees * $loanProductTerm->Value;

            $loanData = [
                "interestRate" => $loanProductTerm->Interest_Rate . '%',
                "interestCycle" => $loanProductTerm->Interest_Cycle,
                "frequencyOfInstallmentRepayment" => $frequencyOfInstallmentRepayment,
                "loanDuration" => round($loanProductTerm->Value / 30, 0) . ' Month(s)',
                "repaymentAmount" => round($dailyRepaymentAmount),
                "totalRepaymentAmount" => round($totalRepaymentAmount),
                "numberOfPayments" => $numberOfPayments,
                'Maturity_Date' => Loan::determineMaturityDate($numberOfPayments ?? $loanProductTerm->Value, $frequencyOfInstallmentRepayment, $loanProductTerm->Value),
                //"requestId" => $requestId,
                "payOff" => round($totalRepaymentAmount + $totalFacilitationFees),
                "facilitationFee" => round($totalFacilitationFees),
                "loanAmount" => $loanAmount,
                //"totalInterest" => $interestCalculated,
                "totalInterest" => 0,
                "feesStructure" => $feesStructure,
            ];
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        return ['loanRecordDetails' => $loanRecordDetails, 'loanData' => $loanData];
    }

    public function applicationNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->id < 10000000) {
                    return 'LA1' . str($this->id)->padLeft(7, '0');
                }

                return 'LA' . $this->id;
            }
        );
    }

    public function pending(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->Credit_Application_Status === 'Pending',
        );
    }
}
