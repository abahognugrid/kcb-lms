<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Partner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super_admin = User::create([
            'name' => 'LMS Admin',
            'email' => 'admin@lms.com',
            'is_admin' => 1,
            'password' => Hash::make('password'),
            'password_changed_at' => now(),
        ]);
        $super_admin->assignRole('Super Admin');

        $partner_admin = User::create([
            'name' => 'KCB Admin',
            'email' => 'admin@kcb.com',
            'is_admin' => false,
            'password' => Hash::make('password'),
            'password_changed_at' => now(),
            'partner_id' => Partner::first()->id
        ]);
        $partner_admin->assignRole('Partner Admin');
    }
}
