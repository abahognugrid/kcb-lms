<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{

  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $this->call(PartnerTableSeeder::class);
    $this->call(RoleSeeder::class);
    $this->call(UserTableSeeder::class);
    $this->call(PermissionSeeder::class);
    $this->call(RolePermissionSeeder::class);
    $this->call(LoanProductTypeSeeder::class);
    $this->call(LoanProductSeeder::class);
    $this->call(SavingsProductSeeder::class);
    $this->call(PartnerApiSettingSeeder::class);
    $this->call(LoanProductPenaltiesSeeder::class);
    $this->call(FloatTopUpSeeder::class);
    $this->call(LoanLossProvisionSeeder::class);
    $this->call(PartnerOvaSeeder::class);
  }
}
