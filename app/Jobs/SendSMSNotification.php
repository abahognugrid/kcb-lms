<?php

namespace App\Jobs;

use App\Services\Contracts\SendViaSMS;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendSMSNotification implements ShouldQueue
{
    use Queueable;

    public SendViaSMS $sendViaSMS;

    /**
     * Create a new job instance.
     */
    public function __construct(SendViaSMS $sendViaSMS)
    {
        $this->sendViaSMS = $sendViaSMS;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->sendViaSMS->send();
    }

    public function shouldQueue(): bool
    {
        return app()->isProduction();
    }
}
