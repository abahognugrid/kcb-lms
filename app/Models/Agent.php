<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function scopeWithTicketCounts($query)
    {
        return $query->withCount([
            'tickets',
            'tickets as open_tickets_count' => function ($q) {
                $q->where('status', 'open');
            },
            'tickets as in_progress_tickets_count' => function ($q) {
                $q->where('status', 'in_progress');
            }
        ]);
    }
}
