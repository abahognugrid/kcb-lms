<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tax extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;


    const TAX_TYPES = [
        'Flat',
        'Percentage',
    ];

    protected $fillable = [
        'name',
        'description',
        'type',
        'rate',
        'amount',
        'is_active',
        'partner_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
