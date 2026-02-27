<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Notifications\SmsNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PastDueLoanReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:past-due-loan-reminders {--loanId=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            if ($this->option('loanId')) {
                $loans = Loan::query()->where('id', $this->option('loanId'))->get();
            } else {
                $loans = Loan::query()->where('Credit_Account_Status', Loan::ACCOUNT_STATUS_OUTSTANDING_AND_BEYOND_TERMS)
                    ->get();
            }

            $count = 0;

            foreach ($loans as $loan) {
                // Get ALL past due schedules for this loan
                $pastDueSchedules = $loan->schedule()
                    ->where('total_outstanding', '>', 0)
                    ->where('payment_due_date', '<', Carbon::now())
                    ->orderBy('payment_due_date', 'asc')
                    ->get();

                if ($pastDueSchedules->isEmpty()) {
                    continue;
                }

                // Calculate total outstanding for all past due installments
                $totalAmount = $pastDueSchedules->sum('total_outstanding');
                $penalties = $loan->getOutstandingPenalties();
                $totalAmount += $penalties;
                $customer = $loan->customer;
                $productName = $loan->product->Name;
                $message = 'Dear ' . $customer->name . ', your ' . $productName . ' of UGX ' .
                    number_format($totalAmount) .
                    ' is OVERDUE. Make payment to avoid a penalty and poor CRB ratings. Thank you.';
                $customer->notify(new SmsNotification(
                    $message,
                    $customer->Telephone_Number,
                    $customer->id,
                    $loan->partner_id,
                    $loan->partner->smsPrice(),
                    $loan->partner->smsCost(),
                ));

                $count += 1;
            }

            $this->info($count . ' customers have been notified to repay their loans');
        } catch (Exception $e) {
            Log::error('Error sending loan reminders: ' . $e->getMessage() .
                ' in file: ' . $e->getFile() .
                ' on line: ' . $e->getLine());
        }
    }
}
