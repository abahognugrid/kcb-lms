<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends \Spatie\ModelStatus\Status
{
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
