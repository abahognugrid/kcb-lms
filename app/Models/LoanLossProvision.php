<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class LoanLossProvision extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $guarded = [];

    protected $casts = [
        'provision_rate' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);

        static::creating(function ($model) {
            $model->ageing_category_slug = Str::slug($model->ageing_category);
        });
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function loanProduct(): BelongsTo
    {
        return $this->belongsTo(LoanProduct::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function journalEntries(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'journable', 'transactable', 'transactable_id');
    }

    public function days(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->maximum_days === 0) {
                    return $this->minimum_days . ' - Above';
                }

                return $this->minimum_days . ' - ' . $this->maximum_days;
            }
        );
    }
}
