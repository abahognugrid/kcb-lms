<?php

namespace App\Services\Account;

use App\Models\Partner;

class SharedSeederServiceHelper
{
    // protected $faker;
    protected $partner;
    protected $partner_id;

    public function __construct($partner_id)
    {
        $this->partner_id = $partner_id;
        $this->partner = Partner::find($partner_id);
        // $this->faker = fake();
    }

    public function randomAmount()
    {
        return rand(100, 10000);
    }
}
