<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Rawilk\Settings\Facades\Settings;

class SetPartnerIdInSessionListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $partnerId = $event->user->partner_id;

        if (empty($partnerId)) {
            return;
        }

        session()->put('partner_id', $partnerId);

        Settings::setTeamId($partnerId);
    }
}
