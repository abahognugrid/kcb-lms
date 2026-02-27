<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\SmsCampaign;
use App\Models\SmsLog;
use App\Notifications\SmsNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SmsCampaignService
{
    public function storeMessages()
    {
        $today = Carbon::today();
        $campaigns = SmsCampaign::whereDate('scheduled_at', $today)
            ->where('status', 'Pending')
            ->get();
        $count = 0;
        $category = 'Campaign';
        foreach ($campaigns as $campaign) {

            $customers = Customer::whereIn('id', json_decode($campaign->customer_ids))
                ->get(['id', 'First_Name', 'Last_Name', 'Telephone_Number']);

            foreach ($customers as $customer) {
                $smsLog = SmsLog::where('Category', $category)
                    ->where('Customer_ID', $customer->id)
                    ->whereDate('created_at', '=', $today)
                    ->first();

                if ($smsLog) {
                    // continue;
                }
                // SmsLog::store($campaign->message, $campaign->partner_id, $customer->id, $category, $customer->Telephone_Number);
                $customer->notify(new SmsNotification($campaign->message, $customer->Telephone_Number, $customer->id, $campaign->partner_id, $campaign->partner->smsPrice(), $campaign->partner->smsCost()));
                $count += 1;
            }


            $campaign->status = 'Completed';
            $campaign->save();
        }
        $printMessage = 'Sms Campaign messages stored successfully - ' . $count . ' sms';
        print($printMessage);
    }
}
