<?php

namespace Database\Seeders;

use App\Helpers\SystemResource;
use App\Helpers\Operations;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $readOnlyResources = SystemResource::getReadOnlySystemResources();
        foreach ($readOnlyResources as $resource) {
            Permission::updateOrCreate([
                'name' => 'view ' . $resource
            ]);
        }
        $crudResources = SystemResource::getCrudSystemResources();
        $supported_operations = Operations::getSupportedOperations();
        foreach ($crudResources as $resource) {
            foreach ($supported_operations as $operation) {
                Permission::updateOrCreate([
                    'name' => $operation . ' ' . $resource
                ]);
            }
        }
    }
}
