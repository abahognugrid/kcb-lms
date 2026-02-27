<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LabelTicket extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'label_ticket';

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
        'label_id',
        'ticket_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'label_id' => 'integer',
        'ticket_id' => 'integer',
    ];

    /**
     * Get the related label.
     */
    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    /**
     * Get the related ticket.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
