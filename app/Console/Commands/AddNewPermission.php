<?php

namespace App\Console\Commands;

use App\Helpers\Operations;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class AddNewPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:add-new-permission {--type=} {permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permission = $this->argument('permission');
        $type = $this->option('type');
        if (!$type) {
            $this->error('No permission type has been provided');
            return 0;
        }
        if ($type == 'read') {
            Permission::updateOrCreate([
                'name' => 'view ' . $permission
            ]);
        }
        if ($type == 'crud') {
            $supported_operations = Operations::getSupportedOperations();
            foreach ($supported_operations as $operation) {
                Permission::updateOrCreate([
                    'name' => $operation . ' ' . $permission
                ]);
            }
        }
        $this->info('Permission: ' . $permission . ' has been created successfully');
    }
}
