<?php

use App\Console\Commands\ApplyLoanPenalties;
use App\Console\Commands\AutoWriteOffAfterDays;
use App\Console\Commands\CleanupLogs;
use App\Console\Commands\FlagOverdueLoans;
use App\Console\Commands\PartnerSmsReminders;
use App\Console\Commands\PastDueLoanReminders;
use App\Console\Commands\SendLoggedSms;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendLoggedSms::class)->everyMinute()->withoutOverlapping();
Schedule::command(PartnerSmsReminders::class)->dailyAt('8am');
Schedule::command(FlagOverdueLoans::class)->dailyAt('00:01');
Schedule::command(ApplyLoanPenalties::class)->dailyAt('00:10'); // Run this just after flagging overdue loans

Schedule::command(PastDueLoanReminders::class)->cron('0 9 */3 * *');
Schedule::command(CleanupLogs::class)->weekly();
// Schedule::command(AutoWriteOffAfterDays::class)->dailyAt('00:15');
