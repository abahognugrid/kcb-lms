<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Partner;
use App\Notifications\SmsNotification;
use Illuminate\Console\Command;

class SendSingleSms extends Command
{
    protected $signature = 'sms:send-single {phone}';

    protected $description = 'Send a single SMS to a specified phone number';

    public function handle()
    {
        try {
            $phone = $this->argument('phone');
            $customer = Customer::where('Telephone_Number', $phone)->first();
            $customer->notify(new SmsNotification('Test sms in queue', $customer->Telephone_Number, $customer->id,  3, 0, 0));
            $this->info('Sms delivered successfully!');
        } catch (\Exception $e) {
            $this->error("Error sending SMS: {$e->getMessage()}");
        }
    }
}
