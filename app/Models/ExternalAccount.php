<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class ExternalAccount extends Model implements Auditable
{
    use AuditingAuditable, HasFactory;

    protected $fillable = [
        'partner_id',
        'disbursement_account',
        'collection_account',
        'service_provider',
    ];

    protected $casts = [
        'disbursement_account' => 'decimal:2',
        'collection_account' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
