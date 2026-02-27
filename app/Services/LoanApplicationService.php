<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LmsUssdSessionTracking;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\LoanProductTerm;
use App\Models\LoanSchedule;
use App\Models\Partner;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoanApplicationService
{
    /**
     * Create a loan application
     *
     * @throws Exception
     */
    public function create(array $data, Partner $partner, Customer $customer): array
    {
        DB::beginTransaction();

        try {
            $requestId = $data['requestId'];
            $loanAmount = $data['loanAmount'];
            $loanTermInDays = $data['loanTermInDays'];
            $frequencyOfInstallmentRepayment = $data['frequencyOfInstallmentRepayment'];
            $loanProductCode = $data['loanProductCode'];
            $loanProductTermCode = $data['loanProductTermCode'];
            $loanProduct = LoanProduct::where('Code', $loanProductCode)->first();

            // Validate customer doesn't have active loans (if partner doesn't allow multiple loans)
            if (!$loanProduct->Allows_Multiple_Loans) {
                $loan = $customer->loans()
                    ->whereNot('Credit_Account_Status', 4) // Fully Paid
                    ->whereNot('Credit_Account_Status', 3) // Written-off
                    ->latest()
                    ->first();
                if ($loan) {
                    throw new Exception('Customer already has an active loan with ' . $loan->partner->Institution_Name . ' of UGX ' . number_format($loan->totalOutstandingBalanceExcludingWriteOffs()) . ', Account Reference: ' . $loan->Credit_Account_Reference, 400);
                }
            } else {
                $loan = $customer->loans()
                    ->where('Credit_Account_Status', Loan::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS) // Beyond terms
                    ->latest()
                    ->first();
                if ($loan) {
                    throw new Exception('Customer already has a loan beyond terms with ' . $loan->partner->Institution_Name . ' of UGX ' . number_format($loan->totalOutstandingBalanceExcludingWriteOffs()) . ', Account Reference: ' . $loan->Credit_Account_Reference, 400);
                }
            }

            $loanProduct = LoanProduct::where('Code', $loanProductCode)->first();

            if (! $loanProduct) {
                throw new Exception('Loan product not found. You provided an invalid loan product code in the body.', 400);
            }

            if ($loanProduct->partner_id != $partner->id) {
                throw new Exception('Loan product provided does not belong to partner provided', 400);
            }

            $loanProductTerm = LoanProductTerm::where('Code', $loanProductTermCode)->first();

            if (! $loanProductTerm) {
                throw new Exception('Loan product term not found. You provided an invalid loan product term code in the body.', 400);
            }
            if ($loanProductTerm->Loan_Product_ID != $loanProduct->id) {
                throw new Exception('Loan product term provided does not belong to the loan product provided, Request: ' . json_encode($data), 400);
            }
            if ($loanTermInDays > $loanProductTerm->Value) {
                throw new Exception('Loan term is too long.', 400);
            }
            if (! in_array($frequencyOfInstallmentRepayment, json_decode($loanProductTerm->Repayment_Cycles))) {
                throw new Exception('Unsupported frequency of installment repayment.', 400);
            }

            $totalRepaymentAmount = 0;
            $dailyRepaymentAmount = 0;
            $settlementFee = 0;
            $interestFees = 0;

            $numberOfPayments = Loan::determineNumberOfPayments($loanTermInDays ?? $loanProductTerm->Value, $frequencyOfInstallmentRepayment);

            if ($loanProductTerm->Interest_Charged_At == 'Disbursement') {
                $totalRepaymentAmount = $loanAmount;
                $dailyRepaymentAmount = $loanAmount;
            } else {
                if ($loanProductTerm->Interest_Calculation_Method == 'Flat') {
                    $totalScheduleAmounts = LoanSchedule::calculateFlatTotalRepaymentAndInterest(
                        $loanAmount,
                        $loanProductTerm->Interest_Rate,
                        $loanTermInDays ?? $loanProductTerm->Value,
                        $frequencyOfInstallmentRepayment,
                        $loanProductTerm->Interest_Cycle,
                    );
                    $totalRepaymentAmount = $totalScheduleAmounts['total_repayment'];
                    $dailyRepaymentAmount = $totalScheduleAmounts['daily_repayment'];
                } elseif ($loanProductTerm->Interest_Calculation_Method == 'Declining Balance - Discounted') {
                    $totalScheduleAmounts = LoanSchedule::calculateDecliningBalanceTotalRepaymentAndInterest(
                        $loanAmount,
                        $loanProductTerm->Interest_Rate,
                        $numberOfPayments,
                        $frequencyOfInstallmentRepayment,
                        $loanProductTerm->Interest_Cycle,
                    );
                    $totalRepaymentAmount = $totalScheduleAmounts['total_repayment'];
                    $dailyRepaymentAmount = $totalScheduleAmounts['daily_repayment'];
                } elseif ($loanProductTerm->Interest_Calculation_Method == 'Amortization') {
                    $totalScheduleAmounts = LoanSchedule::calculateAmortizedTotalRepaymentAndInterest(
                        $loanAmount,
                        $loanProductTerm->Interest_Rate,
                        $numberOfPayments,
                        $frequencyOfInstallmentRepayment,
                        $loanProductTerm->Interest_Cycle,
                    );
                    $totalRepaymentAmount = $totalScheduleAmounts['total_repayment'];
                    $dailyRepaymentAmount = $totalScheduleAmounts['daily_repayment'];
                } elseif ($loanProductTerm->Interest_Calculation_Method == 'Flat on Loan Amount') {
                    $totalScheduleAmounts = LoanSchedule::calculateFlatAmountTotalRepaymentAndInterest(
                        $loanAmount,
                        $loanProductTerm->Interest_Rate,
                        $loanTermInDays ?? $loanProductTerm->Value,
                        $frequencyOfInstallmentRepayment,
                    );
                    $totalRepaymentAmount = $totalScheduleAmounts['total_repayment'];
                    $dailyRepaymentAmount = $totalScheduleAmounts['daily_repayment'];
                }
            }

            $credit_account_type = $loanProduct->loan_product_type; // asset or mobile loan
            $loanRecordDetails = [
                'Request_ID' => $requestId,
                'partner_id' => $partner->id,
                'Customer_ID' => $customer->id,
                'Loan_Product_ID' => $loanProduct->id,
                'Loan_Purpose' => $data['loanPurpose'],
                'Applicant_Classification' => $customer->Classification,
                'Credit_Application_Date' => $data['creditApplicationDate'],
                'Amount' => $loanAmount,
                'Credit_Application_Status' => 'Pending',
                'Credit_Account_or_Loan_Product_Type' => $credit_account_type->Code,
                'Credit_Application_Duration' => '0', // time between application and the time it is approved or rejected. This is auto so zero(0)
                'Client_Consent_flag' => 'Yes',
                'Last_Status_Change_Date' => $data['creditApplicationDate'],
                'Credit_Amount_Approved' => $loanAmount,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // Create a new loan application
            $loan_application = LoanApplication::updateOrCreate([
                'Request_ID' => $requestId,
                'partner_id' => $partner->id,
                'Customer_ID' => $customer->id,
                'Loan_Product_ID' => $loanProduct->id,
            ], $loanRecordDetails);

            $sessionData = [
                'requestId' => $requestId,
                'Customer_Phone_Number' => $customer->Telephone_Number,
                'Loan_Application_ID' => $loan_application->id,
                'Loan_Producd_Code' => $loanProductCode,
                'Loan_Producd_Term_Code' => $loanProductTermCode,
                'Credit_Payment_Frequency' => $frequencyOfInstallmentRepayment,
                'Number_of_Payments' => $numberOfPayments,
                'Date_of_First_Payment' => Loan::determineDateOfFirstPayment($frequencyOfInstallmentRepayment, $loanProductTerm->Value, $data['creditApplicationDate']),
                'Maturity_Date' => Loan::determineMaturityDate($numberOfPayments ?? $loanProductTerm->Value, $frequencyOfInstallmentRepayment, $loanTermInDays, $data['creditApplicationDate']),
            ];

            $fees = $loanProduct->fees;
            $feeAmount = 0;
            $feesStructure = [];
            $facilitationFees = 0;

            // Delete any existing session tracking for this requestId and phone number
            LmsUssdSessionTracking::where('requestId', $requestId)
                ->where('Customer_Phone_Number', $customer->Telephone_Number)
                ->delete();

            // Create a new session tracking record
            $lmsUssdSessionTracking = LmsUssdSessionTracking::create(array_merge(
                $sessionData,
                [
                    'requestId' => $requestId,
                    'Customer_Phone_Number' => $customer->Telephone_Number,
                ],
            ));

            $collectionFees = [];
            $dailyFee = 0;
            // First Pass: Process all fees except those applicable on installments
            foreach ($fees as $fee) {
                $Value = $fee->Value;
                $Calculation_Method = $fee->Calculation_Method;
                $Applicable_On = $fee->Applicable_On;
                $Applicable_At = $fee->Applicable_At;

                if ($Applicable_At == 'Maturity') {
                    continue; // Skip maturity fees for now
                }

                if ($Applicable_At == 'Disbursement') {
                    $feeAmount = $Calculation_Method == 'Flat' ? $Value : ($fee->Value / 100) * $loanAmount;
                    if ($Calculation_Method == 'Percentage') {
                        $feeAmount = ($fee->Value / 100) * $loanAmount;
                    } elseif ($Calculation_Method == 'Tiered') {
                        $tiers = json_decode($fee->Tiers, true);
                        $amount = 0;

                        foreach ($tiers as $tier) {
                            if ($loanAmount >= $tier['min'] && $loanAmount <= $tier['max']) {
                                $amount = $tier['value'];
                                break;
                            }
                        }

                        $feeAmount = $amount;
                    } else {
                        $feeAmount = $fee->Value;
                    }
                } elseif ($Applicable_At == 'Repayment') {
                    if ($Calculation_Method == 'Percentage') {
                        if ($Applicable_On == 'Principal') {
                            $principal = $loanAmount;
                            $feeAmount = ($principal * $Value / 100);
                        } elseif ($Applicable_On == 'Interest') {
                            $interest = $loanProductTerm->Interest_Rate;
                            $feeAmount = ($interest * $Value / 100);
                        } elseif ($Applicable_On == 'Balance') {
                            $principal = $loanAmount;
                            $interest = $loanProductTerm->Interest_Rate;
                            $balance = $principal + $interest;
                            $feeAmount = ($balance * $Value / 100);
                        } elseif ($Applicable_On == 'Installment Balance') {
                            $collectionFees[] = $fee;

                            continue;
                        }
                    } elseif ($Calculation_Method == 'Tiered') {
                        $tiers = json_decode($fee->Tiers, true);
                        $amount = 0;

                        foreach ($tiers as $tier) {
                            if ($loanAmount >= $tier['min'] && $loanAmount <= $tier['max']) {
                                $amount = $tier['value'];
                                break;
                            }
                        }

                        $feeAmount = $amount;
                    } else {
                        $feeAmount = $fee->Value;
                    }

                    $dailyFee = $feeAmount;
                    $dailyRepaymentAmount += $dailyFee;
                    $feeAmount = $feeAmount * $numberOfPayments;
                    $settlementFee += $feeAmount;
                }
                if ($fee->Charge_Interest == 'Yes') {
                    $interestFees += $feeAmount;
                }
                $feesStructure[] = ['name' => $fee->Name, 'calculationMethod' => $Calculation_Method, 'calculationValue' => $Value, 'amount' => 'UGX ' . number_format($feeAmount), 'applicable_at' => $Applicable_At, 'dailyFee' => $dailyFee];
            }

            foreach ($collectionFees as $fee) {
                $Value = $fee->Value;
                $Applicable_At = $fee->Applicable_At;
                $feeAmount = $dailyRepaymentAmount * ($Value / 100);
                $dailyFee = $feeAmount;
                $feeAmount = $feeAmount * $numberOfPayments;
                $feesStructure[] = ['name' => $fee->Name, 'amount' => 'UGX ' . number_format($feeAmount), 'applicable_at' => $Applicable_At, 'dailyFee' => $dailyFee];
                $dailyRepaymentAmount += $dailyFee;
                $settlementFee += $feeAmount;
            }
            $dailyRepaymentAmount += ($feeAmount * (1 + $loanProductTerm->Interest_Rate / 100));
            $totalRepaymentAmount += ($settlementFee + ($feeAmount * (1 + $loanProductTerm->Interest_Rate / 100)));

            $lmsUssdSessionTracking->save();
            $dataUpdate = [
                'Credit_Amount_Approved' => $loanAmount + $interestFees,
                'Amount' => $loanAmount + $interestFees,
            ];

            // Update the loan application
            $loan_application->update($dataUpdate);

            DB::commit();

            $loanDuration = $loanProductTerm->Value > 30 ? round($loanProductTerm->Value / 30, 0) . ' Month(s)' : $loanProductTerm->Value . ' Days';

            return [
                'returnCode' => 0,
                'returnData' => [
                    'interestRate' => $loanProductTerm->Interest_Rate . '%',
                    'interestCycle' => $loanProductTerm->Interest_Cycle,
                    'frequencyOfInstallmentRepayment' => $frequencyOfInstallmentRepayment,
                    'loanDuration' => $loanDuration,
                    'repaymentAmount' => round($dailyRepaymentAmount),
                    'totalRepaymentAmount' => round($totalRepaymentAmount),
                    'requestId' => $requestId,
                    'payOff' => round($totalRepaymentAmount), // removed $totalFacilitationFees because of ABC
                    'facilitationFee' => round($facilitationFees),
                    'loanAmount' => $loanAmount,
                    'settlementFee' => round($settlementFee),
                    'feesStructure' => $feesStructure,
                    'numberOfPayments' => $numberOfPayments,
                    'paymentSwitchStatus' => $loanProduct->switch?->status,
                ],
                'returnMessage' => 'Successfully posted loan application',
                'loanApplication' => $loan_application,
            ];
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error($throwable->getMessage());
            Log::error($throwable->getFile());
            Log::error($throwable->getLine());
            throw $throwable;
        }
    }
}
