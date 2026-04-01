<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupLogs extends Command
{
    protected $signature = 'lms:cleanup-logs';
    protected $description = 'Delete logs older than 30 days';

    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(30);

        DB::table('logs')
            ->where('created_at', '<', $cutoffDate)
            ->delete();
    }
}
