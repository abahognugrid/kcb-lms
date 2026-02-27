<?php

namespace App\Policies;

use App\Models\User;
use Coderflex\LaravelTicket\Models\Ticket;

class TicketPolicy
{
    // public function view(User $user, Ticket $ticket)
    // {
    //     return $user->id === $ticket->user_id || $user->is_admin;
    // }

    // public function update(User $user, Ticket $ticket)
    // {
    //     return $user->is_admin;
    // }

    // public function comment(User $user, Ticket $ticket)
    // {
    //     return $user->id === $ticket->user_id || $user->is_admin;
    // }
}
