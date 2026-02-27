<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = 'Super Admin';
        Role::firstOrCreate([
            'name' => $adminRole,
            'guard_name' => 'web',
        ]);

        $partnerRole = 'Partner Admin';
        Role::firstOrCreate([
            'name' => $partnerRole,
            'guard_name' => 'web',
        ]);
    }
}
