<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SmsLog;
use App\Services\Sms;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendLoggedSms extends Command
{
    protected $signature = 'lms:send-logged-sms';
    protected $description = 'Process pending SMS messages in batches';

    public function handle()
    {
        $this->info('Starting SMS processing...');
        try {
            // Get 20 pending messages ordered by creation time
            $messages = SmsLog::where('Status', 'Pending')
                ->orderBy('created_at')
                ->limit(10)
                ->get();
            if ($messages->isEmpty()) {
                $this->info('No pending messages found.');
                return;
            }
            // Mark messages as selected
            SmsLog::whereIn('id', $messages->pluck('id'))
                ->update(['Status' => 'Selected']);
            $count = 0;
            $failures = 0;
            foreach ($messages as $message) {
                try {
                    $this->info("Processing message ID: {$message->id}");
                    $sms = new Sms();
                    $sent = $sms->sendSms($message->Telephone_Number, $message->Message);
                    if ($sent == true) {
                        $message->update(['Status' => 'Sent']);
                        $count += 1;
                    }
                } catch (\Exception $e) {
                    // Mark as failed and stop processing
                    $message->update(['Status' => 'Failed']);
                    $failures += 1;

                    throw $e; // Re-throw to stop the command
                }
            }

            $this->info('Batch processed successfully.');
        } catch (\Exception $e) {
            $this->error("Processing stopped due to error: " . $e->getMessage());
            return 1; // Exit with error code
        }
        return 0;
    }
}
