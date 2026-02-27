<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Switches;

class SwitchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $switchesData = [
            [
                'name' => 'AIRTEL',
                'category' => 'Payment',
                'environment' => 'Test',
                'status' => 'On',
            ],
            [
                'name' => 'DMARK',
                'category' => 'SMS',
                'environment' => 'Test',
                'status' => 'On',
            ],
            [
                'name' => 'AFRICASTALKING',
                'category' => 'SMS',
                'environment' => 'Test',
                'status' => 'Off',
            ],
        ];

        foreach ($switchesData as $data) {
            Switches::create($data);
        }
    }
}
