<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddGlobalCustomerCare extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:add-global-customer-care';

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
        // create customer care role using raw query
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'Customer Care',
            'partner_id' => null,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $role = Role::find($roleId);
        // create user 
        $user = User::create([
            'name' => 'Customer Care',
            'email' => 'care@lms.com',
            'is_admin' => false,
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);
        $user->assignRole($role);
        // echo done message
        $this->info('Global Customer Care role and user created successfully.');
    }
}
