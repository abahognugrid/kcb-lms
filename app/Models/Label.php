<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Label extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'is_visible'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($label) {
            $label->slug = Str::slug($label->name);
        });

        static::updating(function ($label) {
            $label->slug = Str::slug($label->name);
        });
    }

    /**
     * Scope a query to only include visible labels.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get all tickets associated with this label.
     */
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class)
            ->using(LabelTicket::class);
    }
}
