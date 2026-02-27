<?php

namespace App\Models;

use Coderflex\LaravelTicket\Concerns\HasTickets;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $table = 'tickets'; // or whatever your table is named

    use HasFactory, HasTickets;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'message',
        'priority',
        'status',
        'is_resolved',
        'is_locked',
        'assigned_to',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_resolved' => 'boolean',
        'is_locked' => 'boolean',
    ];

    /**
     * Get the user who created the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'assigned_to');
    }

    /**
     * Scope a query to only include open tickets.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
    /**
     * Scope a query to only include closed tickets.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope a query to only include resolved tickets.
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope a query to only include tickets of a given priority.
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Check if ticket is high priority.
     */
    public function isHighPriority(): bool
    {
        return $this->priority === 'high';
    }

    /**
     * Mark ticket as resolved.
     */
    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'is_resolved' => true
        ]);
    }

    /**
     * Lock the ticket from further comments.
     */
    public function lock(): void
    {
        $this->update(['is_locked' => true]);
    }

    /**
     * Unlock the ticket for comments.
     */
    public function unlock(): void
    {
        $this->update(['is_locked' => false]);
    }

    /**
     * Get the messages for the ticket.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function scopeFilter($query, array $filters)
    {
        // Status filter
        $query->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })
            // Priority filter
            ->when($filters['priority'] ?? null, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            // Category filter (if you have category relationship)
            ->when($filters['category'] ?? null, function ($query, $categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            });
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_ticket');
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'label_ticket');
    }

    protected static function booted(): void
    {
        static::creating(function ($ticket) {
            $ticket->uuid = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
