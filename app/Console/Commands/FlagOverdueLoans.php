<?php

namespace App\Console\Commands;

use App\Actions\Loans\MarkOverDueLoansAction;
use Illuminate\Console\Command;

class FlagOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:flag-overdue-loans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flag overdue loans';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        app(MarkOverDueLoansAction::class)->execute();
        return 0;
    }
}
