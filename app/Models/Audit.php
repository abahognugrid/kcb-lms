<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Models\Audit as BaseAudit;

class Audit extends BaseAudit
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($audit) {
            if (Auth::check()) {
                $audit->partner_id = Auth::user()->partner_id;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
