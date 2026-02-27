<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryTicket extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'category_ticket';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'ticket_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'category_id' => 'integer',
        'ticket_id' => 'integer',
    ];

    /**
     * Get the related category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the related ticket.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
