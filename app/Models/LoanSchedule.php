<?php

namespace App\Models;

use Exception;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LoanSchedule extends Model
{
    use HasFactory, SoftDeletes;

    const DAILY = 'Daily';
    const DailyExcludingSundays = 'DailyExcludingSundays';
    const WEEKLY = 'Weekly';
    const BIWEEKLY = 'Bi-weekly';
    const MONTHLY = 'Monthly';
    const ANNUALLY = 'Annually';
    const ONCE = 'Once';
    const NONE = 'None';

    const FLAT = 'Flat';
    const FLAT_AMOUNT = 'Flat Amount';
    const DECLINING_BALANCE = 'Declining Balance - Discounted';
    const AMORTIZATION = 'Amortization';

    const SUPPORT_INTEREST_METHODS = [
        self::FLAT,
        self::FLAT_AMOUNT,
        self::DECLINING_BALANCE,
        self::AMORTIZATION,
    ];

    const INTEREST_CHARGED_AT = [
        'Repayment',
        'Disbursement'
    ];

    const REPAYMENT_FREQUENCIES = [
        self::ONCE,
        self::DAILY,
        self::DailyExcludingSundays,
        self::WEEKLY,
        self::BIWEEKLY,
        self::MONTHLY,
    ];

    const INTEREST_CYCLES = [
        self::NONE,
        self::DAILY,
        self::WEEKLY,
        self::BIWEEKLY,
        self::MONTHLY,
        self::ANNUALLY
    ];

    protected $guarded = [];

    protected function casts()
    {
        return [
            'payment_due_date' => 'datetime',
        ];
    }

    public static function generateSchedule(Loan $loan)
    {
        try {


            if ($loan->Credit_Application_Status != Loan::APPROVED_STATUS) {
                return;
            }

            $loan_product = $loan->loan_product;
            if (!$loan_product) {
                throw new Exception("Loan product not found for loan {$loan->id}");
            }

            $interest_method = $loan->Interest_Calculation_Method;

            // Clear any existing schedules for this loan
            LoanSchedule::where("loan_id", $loan->id)->forceDelete();

            // Store schedule
            $schedule = [];

            // Handle non-asset loans
            switch ($interest_method) {
                case 'Flat':
                case 'Declining Balance - Discounted':
                case 'Amortization':
                    $principal = $loan->Credit_Amount;
                    $rate = $loan->Interest_Rate;
                    $loanTerm = $loan->Number_of_Payments;

                    $methods = [
                        'Declining Balance - Discounted' => 'generateDecliningBalanceDiscountedSchedule',
                        'Amortization' => 'generateAmortizationSchedule',
                        'Flat' => 'generateFlatSchedule'
                    ];
                    $schedule[] = self::{$methods[$interest_method]}("Loan", $loan, $principal, $rate, $loanTerm, $loan_product->loan_product_terms()->first()->Interest_Cycle, $loan->Credit_Payment_Frequency);
                    break;

                case 'Flat Amount':
                case 'Flat - Total Loan Amount':
                case 'Flat on Loan Amount':
                    $schedule[] = self::generateFlatAmountSchedule("Loan", $loan, $loan_product);
                    break;

                default:
                    throw new Exception("Invalid interest method: $interest_method");
            }

            // Add fixed fees (Monitoring & Collection Fees)
            self::addFixedFeesToSchedule($loan);

            return $schedule;
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Add Monitoring Fees and Collection Fees to the schedule
     */
    public static function addFixedFeesToSchedule(Loan $loan)
    {
        // Retrieve loan schedule grouped by installment number
        $installments = LoanSchedule::where("loan_id", $loan->id)
            ->select(
                'installment_number',
                'payment_due_date',
                DB::raw('SUM(total_outstanding) as total_outstanding_sum')
            )
            ->groupBy('installment_number', 'payment_due_date')
            ->get();

        // Retrieve fees charged at repayment
        $fees = $loan->fees->where('Charge_At', 'Repayment');

        // First pass: Process fees that are NOT based on "Installment Balance"
        foreach ($installments as $installment) {
            foreach ($fees as $fee) {
                if ($fee->loan_product_fee->Applicable_On === 'Installment Balance') {
                    continue; // Skip "Installment Balance" fees for now
                }

                $feeAmount = round($fee->Amount_To_Pay / $loan->Number_of_Payments);

                LoanSchedule::create([
                    'loan_id' => $loan->id,
                    'installment_number' => $installment->installment_number,
                    'total_payment' => $feeAmount,
                    'total_outstanding' => $feeAmount,
                    'payment_due_date' => $installment->payment_due_date,
                    'type' => $fee->loan_product_fee->Name,
                    'payable_to' => $fee->Payable_Account_ID,
                ]);
            }
        }

        // Second pass: Process "Installment Balance" fees, now including previously added fees
        foreach ($installments as $installment) {
            $totalOutstanding = LoanSchedule::where('loan_id', $loan->id)
                ->where('installment_number', $installment->installment_number)
                ->sum('total_outstanding'); // Get updated total outstanding per installment

            foreach ($fees as $fee) {
                if ($fee->loan_product_fee->Applicable_On !== 'Installment Balance') {
                    continue; // Skip non-"Installment Balance" fees
                }

                $feeAmount = round($totalOutstanding * ($fee->loan_product_fee->Value / 100));

                LoanSchedule::create([
                    'loan_id' => $loan->id,
                    'installment_number' => $installment->installment_number,
                    'total_payment' => $feeAmount,
                    'total_outstanding' => $feeAmount,
                    'payment_due_date' => $installment->payment_due_date,
                    'type' => $fee->loan_product_fee->Name,
                    'payable_to' => $fee->loan_product_fee->Payable_Account_ID,
                ]);
            }
        }
    }

    protected static function generateFlatSchedule($type, $loan, $principal, $rate, $term, $interestRateFrequency = 'Monthly', $repaymentFrequency = "Monthly")
    {
        Log::info("Generating flat schedule for loan ID: {$loan->id}, Principal: $principal, Rate: $rate, Term: $term, Interest Rate Frequency: $interestRateFrequency, Repayment Frequency: $repaymentFrequency");
        // Validate inputs
        if (!isset($term) || !isset($rate)) {
            throw new Exception("Loan term or interest rate is not set.");
        }
        // Adjust the rate based on the interest rate frequency
        if ($interestRateFrequency == "Annually") {
            $ratePerPeriod = self::adjustRateForFrequency($rate, $repaymentFrequency);
        } else {
            $ratePerPeriod = $rate;
        }
        $ratePerPeriod = $ratePerPeriod / 100;
        $finalRate = $ratePerPeriod * $term;
        // Calculate total interest for the loan term (flat rate)
        $totalInterest = $principal * $finalRate;
        // Calculate the principal and interest per period
        $principalPerPeriod = round($principal / $term);

        $interestPerPeriod = round($totalInterest / $term);

        // Start date for the schedule
        $startDate = Carbon::parse($loan->Date_of_First_Payment);
        $principalRemaining = $principal;
        $interestRemaining = $totalInterest;
        // Generate the repayment schedule
        $schedule = [];
        if ($repaymentFrequency == 'Once') {
            $dueDate = $loan->Maturity_Date;
            $schedule[] = [
                'installment_number' => 1,
                'loan_id' => $loan->id,
                'principal' => $principalPerPeriod,
                'interest' => $interestPerPeriod,
                'total_payment' => $principalPerPeriod + $interestPerPeriod,
                'payment_due_date' => $dueDate,
                'principal_remaining' => $principalPerPeriod,
                'interest_remaining' => $interestPerPeriod,
                'total_outstanding' => $principalPerPeriod + $interestPerPeriod,
                'type' => $type,
            ];
        } else {
            for ($i = 1; $i <= $term; $i++) {
                if ($i == $term) {
                    $principalPerPeriod = $principalRemaining;
                    $interestPerPeriod = $interestRemaining;
                }
                // Calculate the due date based on the repayment frequency
                $dueDate = self::calculateDueDate($startDate, $i, $repaymentFrequency);
                // Add entry to the schedule
                $schedule[] = [
                    'installment_number' => $i,
                    'loan_id' => $loan->id,
                    'principal' => $principalPerPeriod,
                    'interest' => $interestPerPeriod,
                    'total_payment' => $principalPerPeriod + $interestPerPeriod,
                    'payment_due_date' => $dueDate,
                    'principal_remaining' => $principalPerPeriod,
                    'interest_remaining' => $interestPerPeriod,
                    'total_outstanding' => $principalPerPeriod + $interestPerPeriod,
                    'type' => $type,
                ];
                $principalRemaining -= $principalPerPeriod;
                $interestRemaining -= $interestPerPeriod;
            }
        }


        // Save the schedule to the database
        LoanSchedule::insert($schedule);

        return $schedule;
    }

    // 1. Flat Amount Schedule
    protected static function generateFlatAmountSchedule($type, Loan $loan, LoanProduct $loan_product)
    {
        // Define key parameters from the loan and product
        $principal = $loan->Credit_Amount;
        $loanTerm = $loan->Number_of_Payments ?? $loan->Term;

        // Calculate total interest (constant based on the original principal)
        $interest = $loan->Interest_Rate;
        $totalPayment = $principal + $interest;

        // Determine how much should be paid each period (principal + interest)
        $paymentPerPeriod = $totalPayment / $loanTerm;

        // Repayment frequency (Weekly, Bi-Weekly, Monthly)
        $repaymentFrequency = $loan->Credit_Payment_Frequency;

        // Start date is the disbursement date
        $startDate = Carbon::parse($loan->Date_of_First_Payment);

        // Initialize remaining balances
        $remainingPrincipal = $principal;
        $remainingInterest = $interest;
        $totalOutstandingBalance = $principal + $interest;

        // Initialize an array to hold the schedules
        $schedule = [];
        if ($loanTerm == 1) {
            // Principal and interest for this period
            $principalForPeriod = $principal / $loanTerm;
            $interestForPeriod = $interest / $loanTerm;
            $schedule[] = [
                'installment_number' => 1,
                'loan_id' => $loan->id,
                'principal' => $principalForPeriod,
                'interest' => $interestForPeriod,
                'total_payment' => $principalForPeriod + $interestForPeriod,
                'payment_due_date' => $loan->Maturity_Date,
                'principal_remaining' => $principalForPeriod,
                'interest_remaining' => $interestForPeriod,
                'total_outstanding' => $interestForPeriod + $principalForPeriod,
                'type' => $type,
            ];
        } else {
            // Generate the schedule
            for ($i = 1; $i <= $loanTerm; $i++) {
                // Calculate the due date for each installment
                $dueDate = self::calculateDueDate($startDate, $i, $repaymentFrequency);

                // Principal and interest for this period
                $principalForPeriod = $principal / $loanTerm;
                $interestForPeriod = $interest / $loanTerm;

                // Build the schedule array
                $schedule[] = [
                    'installment_number' => $i,
                    'loan_id' => $loan->id,
                    'principal' => $principalForPeriod,
                    'interest' => $interestForPeriod,
                    'total_payment' => $principalForPeriod + $interestForPeriod,
                    'payment_due_date' => $dueDate,
                    'principal_remaining' => $remainingPrincipal,  // Remaining balance before paymet
                    'interest_remaining' => $remainingInterest,    // Remaining balance before paymet
                    'total_outstanding' => $totalOutstandingBalance,
                    'type' => $type,
                ];

                // Update remaining balances after this period
                $remainingPrincipal -= $principalForPeriod;
                $remainingInterest -= $interestForPeriod;
                $totalOutstandingBalance -= $paymentPerPeriod;
            }
        }
        // Save the schedule to the database
        LoanSchedule::insert($schedule);

        // Return the generated schedule
        return $schedule;
    }


    // 3. Declining Balance - Discounted Schedule
    protected static function generateDecliningBalanceDiscountedSchedule($type, $loan, $principal, $rate, $term, $interestRateFrequency, $repaymentFrequency)
    {
        $loanTerm = $term;

        $startDate = Carbon::parse($loan->Date_of_First_Payment);

        // Initialize the schedule array and set the outstanding balance to the full principal
        $schedule = [];
        $outstandingBalance = $principal;

        // Adjust the rate based on the repayment frequency
        if ($interestRateFrequency == "Annually") {
            $ratePerPeriod = self::adjustRateForFrequency($rate, $repaymentFrequency);
        } else {
            $ratePerPeriod = $rate;
        }
        $ratePerPeriod  = $ratePerPeriod / 100;

        // Constant principal repayment
        $principalRepayment = $principal / $loanTerm;

        if ($loanTerm == 1) {
            $interestForPeriod = $outstandingBalance * $ratePerPeriod;
            $schedule[] = [
                'installment_number' => 1,
                'loan_id' => $loan->id,
                // TODO Alan:Allan:Abaho: Round these off base on the loan product setting
                'principal' => $principalRepayment,
                'interest' => $interestForPeriod,
                'total_payment' => $principalRepayment + $interestForPeriod,
                'payment_due_date' => self::calculateDueDate($startDate, 1, $repaymentFrequency),
                'principal_remaining' => $principalRepayment,
                'interest_remaining' => $interestForPeriod,
                'total_outstanding' => $principalRepayment + $interestForPeriod,
            ];
        } else {
            for ($i = 1; $i <= $loanTerm; $i++) {
                // Interest for the period, calculated on the declining balance
                $interestForPeriod = $outstandingBalance * $ratePerPeriod;

                // Total payment = principal repayment + interest on outstanding balance
                $totalPayment = $principalRepayment + $interestForPeriod;

                // Calculate due date based on frequency
                $dueDate = self::calculateDueDate($startDate, $i, $repaymentFrequency);

                // Add the schedule entry
                $schedule[] = [
                    'installment_number' => $i,
                    'loan_id' => $loan->id,
                    'principal' => $principalRepayment,
                    'interest' => $interestForPeriod,
                    'total_payment' => $totalPayment,
                    'payment_due_date' => $dueDate,
                    'principal_remaining' => $principalRepayment,
                    'interest_remaining' => $interestForPeriod,
                    'total_outstanding' => $interestForPeriod + $principalRepayment,
                    'type' => $type,
                ];

                // Update the outstanding balance for the next period
                $outstandingBalance -= $principalRepayment;
            }
        }

        // Save the schedule to the database
        LoanSchedule::insert($schedule);

        return $schedule;
    }

    protected static function generateAmortizationSchedule($type, $loan, $principal, $rate, $term, $interestRateFrequency, $repaymentFrequency)
    {
        $loanTerm = $term;
        $startDate = Carbon::parse($loan->Date_of_First_Payment);
        $schedule = [];

        if ($interestRateFrequency == "Annually") {
            $ratePerPeriod = self::adjustRateForFrequency($rate, $repaymentFrequency);
        } else {
            $ratePerPeriod = $rate;
        }

        $ratePerPeriod  = $ratePerPeriod / 100;

        // Calculate fixed monthly installment (Amortized Payment)
        $instalmment = ($principal * $ratePerPeriod) / (1 - pow(1 + $ratePerPeriod, -$loanTerm));

        $remainingPrincipal = $principal; // Track remaining balance

        for ($i = 1; $i <= $loanTerm; $i++) {
            $interestForPeriod = $remainingPrincipal * $ratePerPeriod;
            $principalRepayment = $instalmment - $interestForPeriod;
            $dueDate = self::calculateDueDate($startDate, $i, $repaymentFrequency);

            $schedule[] = [
                'installment_number' => $i,
                'loan_id' => $loan->id,
                'principal' => $principalRepayment,
                'interest' => $interestForPeriod,
                'total_payment' => $instalmment,
                'payment_due_date' => $dueDate,
                'principal_remaining' => $principalRepayment, // Remaining principal before this installment is paid
                'interest_remaining' => $interestForPeriod,
                'total_outstanding' => $principalRepayment + $interestForPeriod, // Corrected formula
                'type' => $type,
            ];

            $remainingPrincipal -= $principalRepayment; // Deduct principal repayment
        }

        LoanSchedule::insert($schedule);
        return $schedule;
    }


    protected static function calculateDueDate($startDate, $periodIndex, $frequency)
    {
        if (config('lms.loans.enable_ageing')) {
            $startDate = now()->subDays(config('lms.loans.back_date_days'));
        } else {
            $startDate = now(); // Why are we overriding date here instead of using the parameter.
        }
        $startDate = $startDate->copy()->startOfDay(); // Ensure we're working with a consistent time

        switch ($frequency) {
            case 'Once':
                return $startDate->copy()->addDays($periodIndex);
            case 'Daily':
                return $startDate->addDays($periodIndex);
            case 'DailyExcludingSundays':
                $dueDate = $startDate->copy()->addDays(7);
                $daysAdded = 0;
                while ($daysAdded < $periodIndex) {
                    $dueDate->addDay();
                    if ($dueDate->dayOfWeek !== Carbon::SUNDAY) {
                        $daysAdded++;
                    }
                }
                return $dueDate;
            case 'Weekly':
                return $startDate->addWeeks($periodIndex);
            case 'Bi-weekly':
                return $startDate->addWeeks($periodIndex * 2);
            case 'Monthly':
                return $startDate->addMonths($periodIndex);
            default:
                throw new Exception("Invalid repayment frequency from loan product while generating loan schedule: $frequency");
        }
    }
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function smsLogs()
    {
        return $this->hasManyThrough(
            SmsLog::class,
            Loan::class,
            'id',           // Foreign key on the loans table (Loan ID)
            'Customer_ID',  // Foreign key on the sms_logs table (Customer ID)
            'loan_id',      // Local key on the loan_schedules table (Loan ID)
            'Customer_ID'   // Local key on the loans table (Customer ID)
        );
    }
    public static function calculateFlatTotalRepaymentAndInterest(
        $principal,
        $rate,
        $loanTermInDays,
        $repaymentCycle = 'Monthly',
        $interestCycle = 'Annually'
    ) {
        // Convert rate to decimal (e.g., 8% -> 0.08)
        $rate /= 100;

        // Convert interest rate based on the cycle
        switch ($interestCycle) {
            case 'None':
                $totalInterest = $principal * $rate * 1;
                break;
            case 'Daily':
                $totalInterest = ($principal * $rate * $loanTermInDays);
                break;
            case 'Weekly':
                $totalInterest = ($principal * $rate * ($loanTermInDays / 7));
                break;
            case 'Monthly':
                $totalInterest = ($principal * $rate * ($loanTermInDays / 30));
                break;
            case 'Annually':
                $totalInterest = ($principal * $rate * ($loanTermInDays / 365));
                break;
            default:
                throw new InvalidArgumentException("Invalid interest cycle.");
        }

        // Calculate total repayment
        $totalRepayment = $principal + $totalInterest;

        // Determine the number of repayment periods
        switch ($repaymentCycle) {
            case 'Once':
                $periods = 1;
                break;
            case 'Daily':
                $periods = $loanTermInDays;
                break;
            case 'DailyExcludingSundays':
                // Calculate number of Sundays in the loan period
                $startDate = new DateTime(); // assuming loan starts today
                $endDate = clone $startDate;
                $endDate->add(new DateInterval("P{$loanTermInDays}D"));

                $sundays = 0;
                $interval = new DateInterval('P1D');
                $period = new DatePeriod($startDate, $interval, $endDate);

                foreach ($period as $day) {
                    if ($day->format('w') == 0) { // 0 = Sunday
                        $sundays++;
                    }
                }
                $periods = $loanTermInDays - $sundays;
                break;
            case 'Weekly':
                $periods = ceil($loanTermInDays / 7);
                break;
            case 'Monthly':
                $periods = ceil($loanTermInDays / 30);
                break;
            case 'Annually':
                $periods = ceil($loanTermInDays / 365);
                break;
            default:
                throw new InvalidArgumentException("Invalid repayment cycle.");
        }

        // Calculate payment per period
        $paymentPerPeriod = $totalRepayment / $periods;

        return [
            'total_repayment' => round($totalRepayment, 2),
            'total_interest' => round($totalInterest, 2),
            'daily_repayment' => round($paymentPerPeriod, 2),
        ];
    }



    /**
     * Calculate total repayment and total interest for a Flat Amount loan.
     */
    public static function calculateFlatAmountTotalRepaymentAndInterest($principal, $flatAmount, $loanTermInDays, $frequencyOfInstallmentRepayment = 'Monthly')
    {
        $totalInterest = $flatAmount * $loanTermInDays;
        $totalRepayment = $principal + $totalInterest;

        return [
            'daily_repayment' => $totalRepayment / $loanTermInDays,
            'total_repayment' => $totalRepayment,
            'total_interest' => $totalInterest
        ];
    }

    /**
     * Calculate total repayment and total interest for a Declining Balance loan.
     */
    public static function calculateDecliningBalanceTotalRepaymentAndInterest(
        $principal,
        $rate,
        $numberOfInstallments,
        $frequencyOfInstallmentRepayment = 'Monthly',
        $interestRateFrequency = 'Monthly'
    ) {
        // Adjust rate per period based on frequency
        if ($interestRateFrequency == "Annually") {
            $ratePerPeriod = self::adjustRateForFrequency($rate, $frequencyOfInstallmentRepayment) / 100;
        } else {
            $ratePerPeriod = $rate / 100;
        }

        $totalInterest = 0;
        $outstandingBalance = $principal;
        $principalRepayment = $principal / $numberOfInstallments;

        // Iterate through each installment period
        for ($i = 0; $i < $numberOfInstallments; $i++) {
            // Calculate interest for the current period
            $interestForPeriod = $outstandingBalance * $ratePerPeriod;

            // Update total interest
            $totalInterest += $interestForPeriod;

            // Reduce outstanding balance by the principal repayment
            $outstandingBalance -= $principalRepayment;

            // Ensure outstanding balance does not become negative due to rounding
            if ($outstandingBalance < 0) {
                $outstandingBalance = 0;
            }
        }
        // Calculate total repayment
        $totalRepayment = $principal + $totalInterest;

        // Return results as an array
        return [
            'daily_repayment' => $totalRepayment / $numberOfInstallments,
            'total_repayment' => $totalRepayment,
            'total_interest' => $totalInterest,
        ];
    }

    /**
     * Calculate total repayment and total interest for an Amortized loan.
     */
    public static function calculateAmortizedTotalRepaymentAndInterest(
        $principal,
        $rate,
        $numberOfInstallments,
        $frequencyOfInstallmentRepayment = 'Monthly',
        $interestRateFrequency = 'Monthly'
    ) {
        // Adjust rate per period based on frequency
        if ($interestRateFrequency == "Annually") {
            $ratePerPeriod = self::adjustRateForFrequency($rate, $frequencyOfInstallmentRepayment) / 100;
        } else {
            $ratePerPeriod = $rate / 100;
        }

        // Calculate fixed monthly installment using the amortization formula
        if ($ratePerPeriod > 0) {
            $monthlyInstallment = ($principal * $ratePerPeriod) / (1 - pow(1 + $ratePerPeriod, -$numberOfInstallments));
        } else {
            // If the rate is 0, it's simply an equal division of the principal
            $monthlyInstallment = $principal / $numberOfInstallments;
        }

        $totalInterest = 0;
        $outstandingBalance = $principal;

        // Iterate through each installment period
        for ($i = 0; $i < $numberOfInstallments; $i++) {
            // Calculate interest for the current period
            $interestForPeriod = $outstandingBalance * $ratePerPeriod;

            // Update total interest
            $totalInterest += $interestForPeriod;

            // Calculate principal repayment for the current period
            $principalRepayment = $monthlyInstallment - $interestForPeriod;

            // Reduce outstanding balance
            $outstandingBalance -= $principalRepayment;

            // Ensure outstanding balance does not become negative due to rounding
            if ($outstandingBalance < 0) {
                $outstandingBalance = 0;
            }
        }

        // Calculate total repayment
        $totalRepayment = $principal + $totalInterest;

        // Return results as an array
        return [
            'daily_repayment' => $totalRepayment / $numberOfInstallments,
            'total_repayment' => $totalRepayment,
            'total_interest' => $totalInterest,
        ];
    }

    /**
     * Adjust the rate based on the frequency of installment repayment.
     */
    protected static function adjustRateForFrequency($annualRate, $frequencyOfInstallmentRepayment, $loanTermInDays = null)
    {
        // Apply daily pro-rated rate only if loan term is shorter than 30 days and frequency is daily or daily excluding Sundays
        if (
            $loanTermInDays !== null && $loanTermInDays < 30 &&
            ($frequencyOfInstallmentRepayment === 'Daily' || $frequencyOfInstallmentRepayment === 'DailyExcludingSundays')
        ) {

            // For DailyExcludingSundays, we need to calculate actual working days
            if ($frequencyOfInstallmentRepayment === 'DailyExcludingSundays') {
                $startDate = new DateTime(); // assuming loan starts today
                $endDate = clone $startDate;
                $endDate->add(new DateInterval("P{$loanTermInDays}D"));

                $sundays = 0;
                $interval = new DateInterval('P1D');
                $period = new DatePeriod($startDate, $interval, $endDate);

                foreach ($period as $day) {
                    if ($day->format('w') == 0) { // 0 = Sunday
                        $sundays++;
                    }
                }
                $workingDays = $loanTermInDays - $sundays;
                return ($annualRate / 365) * $workingDays;
            }

            // Regular daily calculation
            return ($annualRate / 365) * $loanTermInDays;
        }

        // Otherwise, calculate based on the repayment frequency
        switch ($frequencyOfInstallmentRepayment) {
            case 'Once':
                return $annualRate;
            case 'Daily':
            case 'DailyExcludingSundays': // Same as Daily for annual rate adjustment
                return $annualRate / 365;
            case 'Weekly':
                return $annualRate / 52;
            case 'Bi-weekly':
                return $annualRate / 26;
            case 'Monthly':
                return $annualRate / 12;
            default:
                throw new Exception("Invalid repayment frequency: $frequencyOfInstallmentRepayment");
        }
    }
}
