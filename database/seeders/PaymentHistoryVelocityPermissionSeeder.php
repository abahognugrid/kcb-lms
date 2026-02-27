<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PaymentHistoryVelocityPermissionSeeder extends Seeder
{
    public function run()
    {
        // Create the permission
        $permission = Permission::firstOrCreate([
            'name' => 'view payment-history-velocity-report',
            'guard_name' => 'web'
        ]);

        // Assign to admin and manager roles (adjust as needed)
        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }

        if ($managerRole) {
            $managerRole->givePermissionTo($permission);
        }
    }
}
