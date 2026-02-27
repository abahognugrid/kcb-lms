<?php

namespace App\Services;

use App\Models\Loan;
use Carbon\Carbon;
use App\Notifications\SmsNotification;
use Illuminate\Support\Facades\Log;

class LoanRepaymentReminderService
{
    public function sendReminders()
    {
        $loans = Loan::where('Credit_Account_Status', Loan::ACCOUNT_STATUS_CURRENT_AND_WITHIN_TERMS)
            ->whereHas('loan_product.smsTemplates')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($loans as $loan) {
            $schedules = $loan->schedule()
                ->where('total_outstanding', '>', 0)
                ->orderBy('installment_number', 'asc')
                ->get()
                ->groupBy('installment_number')
                ->first();

            if (!$schedules) continue;
            $totalAmount = $schedules->sum('total_outstanding');
            $paymentDate = $schedules->first()->payment_due_date;

            $customer = $loan->customer;
            if (!$loan->loan_product->smsTemplates) continue;

            foreach ($loan->loan_product->smsTemplates as $template) {
                $smsDate = Carbon::parse($paymentDate)
                    ->addDays((int)$template->Day)
                    ->format('Y-m-d');
                if ($smsDate !== now()->format('Y-m-d')) continue;
                try {
                    $smsText = str_replace(
                        [':Amount', ':Partner', ':Date', ':ProductName'],
                        [
                            number_format($totalAmount),
                            $loan->partner->Institution_Name,
                            $paymentDate->format('Y-m-d'),
                            $loan->product->Name
                        ],
                        $template->Template
                    );
                    $customer->notify(new SmsNotification(
                        $smsText,
                        $customer->Telephone_Number,
                        $customer->id,
                        $loan->partner_id,
                        $loan->partner->smsPrice(),
                        $loan->partner->smsCost(),
                    ));
                } catch (\Throwable $e) {
                    Log::error('SMS Failed: ' . $e->getMessage(), [
                        'loan_id' => $loan->id,
                        'customer' => $customer->id
                    ]);
                }
            }
        }
    }
}
