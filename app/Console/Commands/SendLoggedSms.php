<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SmsLog;
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
                try {
                    $this->info("Processing message ID: {$message->id}");

                    $sent = $this->sendSms($message->Telephone_Number, $message->Message);
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

    protected function sendSms($phoneNumber, $message)
    {
        if (app()->isLocal()) {
            Log::info('Sms sent to phone: ' . $phoneNumber . ', Content: ' . $message);
            return true;
        }
        $response = Http::get(config('sms.kcb.base_url') . '/api/kcb/sendsms', [
            'action'      => 'sendmessage',
            'username'    => config('sms.kcb.username'),
            'password'    => config('sms.kcb.password'),
            'recipient'   => '256700460055', // change this to $phone
            'messagetype' => 'SMS:TEXT',
            'messagedata' => $message,
        ]);

        if ($response->successful()) {
            return true;
        }

        // Optionally, you can throw an exception or handle errors here
        throw new Exception('Failed to send SMS: ' . $response->body());
    }
}
