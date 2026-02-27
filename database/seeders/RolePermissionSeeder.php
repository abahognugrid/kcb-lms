<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    const admin_only_permissions = [
        'view sms-float-top-up',
        'create sms-float-top-up',
        'delete sms-float-top-up',
        'update sms-float-top-up',

        'view switches',
        'create switches',
        'update switches',
        'delete switches',

        'view partners',
        'create partners',
        'delete partners',

        'update tickets',
        'delete tickets',

        'view agents',
        'create agents',
        'update agents',
        'delete agents',

        'update float-management',
        'delete float-management',

        'view exclusion-parameters',
        'create exclusion-parameters',
        'delete exclusion-parameters',

        'view business-rules',
        'create business-rules',
        'delete business-rules',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_role = Role::findByName('Super Admin');

        foreach (Permission::all() as $permission) {
            $admin_role->givePermissionTo($permission);
            if (!in_array($permission->name, self::admin_only_permissions)) {
                $partner_admin_roles = Role::where('name', 'like', '%Partner Admin%')->get();
                foreach ($partner_admin_roles as $role) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}
