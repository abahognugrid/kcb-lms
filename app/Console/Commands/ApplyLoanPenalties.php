<?php

namespace App\Console\Commands;

use App\Enums\AccountingType;
use App\Models\Loan;
use App\Models\LoanPenalty;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApplyLoanPenalties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:apply-loan-penalties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Applies penalties on overdue loans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $now = Carbon::now();
            // Get all loan schedules with an outstanding balance
            $loans = Loan::query()
                ->has('loan_product.penalties') // Ensures that we only get loans that have penalty to be applied.
                ->whereDate('Maturity_Date', '<', $now)
                ->whereIn('Credit_Account_Status', [Loan::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS])
                ->get();

            foreach ($loans as $loan) {
                $penalty = $loan->productPenalties()->first();

                if (!$penalty) {
                    $this->info("No penalty defined for Loan Product: {$loan->product->Name}");
                    continue;
                }

                $outstandingDays = max(0, $loan->getOutstandingDays());
                $lastUnPaidDate = $loan->schedule()
                    ->where('payment_due_date', '<', now())
                    ->where('total_outstanding', '>', 0)
                    ->min('payment_due_date');

                if ($penalty->Applicable_On === 'Total Outstanding Balance') {
                    $baseAmount = $loan->totalOutstandingBalance();
                } else if ($penalty->Applicable_On === 'Overdue Principal') {
                    $baseAmount = $loan->getOutstandingPrincipal();
                } else if ($penalty->Applicable_On === 'Overdue Interest') {
                    $baseAmount = $loan->getOutstandingInterest();
                } else if ($penalty->Applicable_On === 'Overdue Principal And Interest') {
                    $baseAmount = $loan->getOutstandingPrincipal() + $loan->getOutstandingInterest();
                } else {
                    $this->error("Unknown penalty application form: {$penalty->Applicable_On}");
                    continue;
                }

                $penaltyRate = floatval($penalty->Value) / 100;
                $numberOfPeriods = 1;
                $graceDays = (int) ($penalty->Penalty_Starts_After_Days ?? 0);

                $periodType = strtolower($penalty->Recurring_Penalty_Interest_Period_Type ?? '');
                $startDate = Carbon::parse($lastUnPaidDate)->copy()->addDays($graceDays + 1);

                if ($penalty->Has_Recurring_Penalty) {
                    $penaltyRate = !empty($penalty->Recurring_Penalty_Interest_Value)
                        ? floatval($penalty->Recurring_Penalty_Interest_Value) / 100
                        : $penaltyRate;

                    $numberOfPeriods = match ($periodType) {
                        'weekly' => max(1, ceil($outstandingDays / 7)),
                        'monthly' => max(1, ceil($outstandingDays / 30)),
                        'daily' => max(1, $outstandingDays),
                        default => min(1, $outstandingDays),
                    };

                    // Limit to penalty period cap
                    if (!empty($penalty->Recurring_Penalty_Interest_Period_Value)) {
                        $numberOfPeriods = min($numberOfPeriods, (int) $penalty->Recurring_Penalty_Interest_Period_Value);
                    }

                    // Apply grace period
                    if ($graceDays > 0) {
                        $gracePeriods = match ($periodType) {
                            'weekly' => ceil($graceDays / 7),
                            'monthly' => ceil($graceDays / 30),
                            'daily' => $graceDays,
                            default => 0,
                        };
                        $numberOfPeriods = max(0, $numberOfPeriods - $gracePeriods);
                    }
                }

                // Calculate total and new penalty amount
                $penaltyAmount = round($baseAmount * $penaltyRate * $numberOfPeriods);
                $penaltiesAlreadyAccrued = LoanPenalty::where('Loan_ID', $loan->id)
                    ->where('Product_Penalty_ID', $penalty->id)
                    ->sum('Amount_To_Pay') ?? 0;

                $additionalPenalties = $penaltyAmount - $penaltiesAlreadyAccrued;

                if ($additionalPenalties <= 0) {
                    $this->info("No penalty to apply for Loan ID: {$loan->id}, Penalty ID: {$penalty->id}");
                    continue;
                }

                $existingPeriods = LoanPenalty::where('Loan_ID', $loan->id)
                    ->where('Product_Penalty_ID', $penalty->id)
                    ->count();

                $additionalPeriods = $numberOfPeriods - $existingPeriods;

                if ($additionalPeriods <= 0) {
                    $this->info("No new penalty periods for Loan ID: {$loan->id}, Penalty ID: {$penalty->id}");
                    continue;
                }

                // Distribute the additional penalty across the remaining periods
                for ($i = 0; $i < $numberOfPeriods; $i++) {
                    $alreadyExists = LoanPenalty::where('Loan_ID', $loan->id)
                        ->where('Product_Penalty_ID', $penalty->id)
                        ->where('date', $startDate)
                        ->exists();

                    if ($alreadyExists) {
                        $startDate = $this->getNextPenaltyDate($periodType, $startDate);
                        continue;
                    }

                    DB::transaction(function () use ($loan, $startDate, $additionalPenalties, $additionalPeriods, $penalty) {
                        LoanPenalty::create([
                            'partner_id' => $loan->partner_id,
                            'Loan_ID' => $loan->id,
                            'Customer_ID' => $loan->Customer_ID,
                            'date' => $startDate,
                            'Amount' => 0,
                            'Amount_To_Pay' => round($additionalPenalties / $additionalPeriods, 2),
                            'Product_Penalty_ID' => $penalty->id,
                        ]);
                    });

                    $startDate = $this->getNextPenaltyDate($periodType, $startDate);
                }

                $this->info("Applied new penalty of {$additionalPenalties} to Loan ID: {$loan->id}, Penalty ID: {$penalty->id} for {$additionalPeriods} periods");
            }

            $this->info('Loan penalties applied successfully.');
            return 0;
        } catch (Exception $e) {
            $this->error($e->getMessage());
            Log::error($e->getMessage());
            return 1;
        }
    }


    protected function savePenaltyHistory(array $data)
    {
        return Storage::disk('local')->append('penalty_history.csv', $data);
    }

    private function getNextPenaltyDate(string $periodType, string $startDate): string
    {
        return match ($periodType) {
            'weekly' => Carbon::parse($startDate)->addWeek(),
            'monthly' => Carbon::parse($startDate)->addMonth(),
            'daily' => Carbon::parse($startDate)->addDay(),
            default => Carbon::parse($startDate)->addDay(),
        };
    }
}
