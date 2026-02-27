<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsFloatTopup extends Model
{
    use HasFactory;

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }
}
