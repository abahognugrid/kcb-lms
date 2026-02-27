<?php

namespace App\Console\Commands;

use App\Actions\CreateProvisionAccountsAction;
use App\Models\Partner;
use Illuminate\Console\Command;

class CreateProvisionAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:create-provision-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create provision accounts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Partner::query()->each(function (Partner $partner) {
            $this->info('Creating or updating provision accounts for: ' . $partner->Institution_Name);

            app(CreateProvisionAccountsAction::class)->execute($partner);
        });

        return 0;
    }
}
