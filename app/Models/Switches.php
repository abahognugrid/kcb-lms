<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Switches extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'partner_id',
        'category',
        'environment',
        'status',
        'username',
        'password',
        'sender_id'
    ];

    public const STATUSES = [
        'On',
        'Off'
    ];

    public const CATEGORIES = [
        'Payment',
        'SMS'
    ];

    public const ENVIRONMENTS = [
        'Production',
        'Test'
    ];

    public function partner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
