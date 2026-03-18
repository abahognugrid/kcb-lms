<?php

namespace App\Console\Commands;

use App\Actions\Loans\WriteOffLoanAction;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoWriteOffAfterDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:auto-write-off-after-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically write off loans that have exceeded their auto write-off days threshold';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->info('Starting auto write-off process...');
            Log::info('Starting auto write-off process...');

            $eligibleLoans = $this->getEligibleLoans();
            if ($eligibleLoans->isEmpty()) {
                $this->info('No loans eligible for auto write-off found.');
                Log::info('No loans eligible for auto write-off found.');

                return self::SUCCESS;
            }

            $this->info("Found {$eligibleLoans->count()} loan(s) eligible for auto write-off.");
            Log::info("Found {$eligibleLoans->count()} loan(s) eligible for auto write-off.");

            $this->processWriteOffs($eligibleLoans);

            $this->info('Auto write-off process completed successfully.');
            Log::info('Auto write-off process completed successfully.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error during auto write-off: ' . $e->getMessage());
            Log::error('Error during auto write-off: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Get loans that are eligible for auto write-off.
     * Only retrieves loans that meet all criteria for write-off.
     */
    private function getEligibleLoans(): \Illuminate\Database\Eloquent\Collection
    {
        $today = Carbon::now()->startOfDay();

        return Loan::with('product')
            ->where('Credit_Account_Status', Loan::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS)
            ->whereRelation('loan_product', function ($query) {
                $query->where('Arrears_Auto_Write_Off_Days', '>', 0);
            })
            ->where('Maturity_Date', '<', $today)
            ->whereRaw(
                '(?::date - "loans"."Maturity_Date"::date) > (
            SELECT "Arrears_Auto_Write_Off_Days"
            FROM "loan_products"
            WHERE "loan_products"."id" = "loans"."Loan_Product_ID"
        )',
                [$today]
            )
            ->get();
    }

    /**
     * Process write-offs for the given loans.
     */
    private function processWriteOffs($loans): void
    {
        $writeOffAction = app(WriteOffLoanAction::class);
        $processedCount = 0;
        $errorCount = 0;

        foreach ($loans as $loan) {
            try {
                $writeOffDays = $loan->product->Arrears_Auto_Write_Off_Days ?? 0;
                $daysOverdue = Carbon::now()->startOfDay()->diffInDays($loan->Maturity_Date, true);

                $this->info("Processing loan ID {$loan->id} - Days overdue: {$daysOverdue}, Write-off threshold: {$writeOffDays}");

                $details = [
                    'write_off_date' => now()->toDateString(),
                ];

                $writeOffAction->execute($loan, $details);
                $processedCount++;

                $this->info("Successfully wrote off loan ID {$loan->id}");
                Log::info("Successfully wrote off loan ID {$loan->id}", [
                    'loan_id' => $loan->id,
                    'days_overdue' => $daysOverdue,
                    'write_off_days' => $writeOffDays,
                ]);
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Failed to write off loan ID {$loan->id}: " . $e->getMessage());
                Log::error("Failed to write off loan ID {$loan->id}", [
                    'loan_id' => $loan->id,
                    'error' => $e->getMessage(),
                    'exception' => $e,
                ]);
            }
        }

        $this->info("Write-off processing completed. Successfully processed: {$processedCount}, Errors: {$errorCount}");
        Log::info('Write-off processing completed', [
            'total_loans' => $loans->count(),
            'successfully_processed' => $processedCount,
            'errors' => $errorCount,
        ]);
    }
}
