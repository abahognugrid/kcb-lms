<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SmsLog;
use App\Models\Switches;
use App\Services\Sms;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendLoggedSms extends Command
{
    protected $signature = 'lms:send-logged-sms';
    protected $description = 'Process pending SMS messages in batches';
    // Custom logger instance
    protected $smsLogger;

    public function __construct()
    {
        parent::__construct();
        $this->smsLogger = Log::channel('sms');
    }
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
                $switch = Switches::where('category', 'SMS')->where('status', 'On')->where('partner_id', $message->partner_id)->first();
                $senderID = $switch?->sender_id ?? 'ATUpdates';
                try {
                    $this->info("Processing message ID: {$message->id}");

                    $sent = $this->sendSms($message->Telephone_Number, $message->Message, $senderID);
                    if ($sent == true) {
                        $message->update(['Status' => 'Sent']);
                        $count += 1;
                    }
                } catch (\Exception $e) {
                    $this->smsLogger->error("Failed to dispatch job for message ID: {$message->id}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    // Mark as failed and stop processing
                    $message->update(['Status' => 'Failed']);
                    $failures += 1;

                    throw $e; // Re-throw to stop the command
                }
            }

            $this->info('Batch processed successfully.');
        } catch (\Exception $e) {
            $this->error("Processing stopped due to error: " . $e->getMessage());
            $this->smsLogger->error("PROCESSING STOPPED DUE TO ERROR: " . $e->getMessage());
            return 1; // Exit with error code
        }
        $this->smsLogger->info('Sent: ' . $count);
        $this->smsLogger->info('Failed: ' . $failures);
        return 0;
    }

    protected function sendSms($phoneNumber, $message, $senderID)
    {
        if (local() || testing() || staging()) {
            Log::info('Sms sent to phone: ' . $phoneNumber . ', Content: ' . $message . 'SenderID: ' . $senderID);
            return true;
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
            'apiKey' => 'atsk_b7c2e7d96802d6c0eaf881aa8b551298d81144de826465994a942f803bb8fca1d966b266',
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => ('loanmarketsms'),
            'from' => $senderID,
            'to' => ($phoneNumber), // Phone number(s)
            'message' => ($message) // Custom message
        ]);

        if ($response->successful()) {
            return true;
        }

        // Optionally, you can throw an exception or handle errors here
        throw new Exception('Failed to send SMS: ' . $response->body());
    }
}
