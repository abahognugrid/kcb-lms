<?php

namespace App\Console\Commands;

use App\Services\LoanRepaymentReminderService;
use Illuminate\Console\Command;

class PartnerSmsReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner-sms-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind borrowers to pay their loan';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reminderService = new LoanRepaymentReminderService();
        $reminderService->sendReminders();
    }
}
