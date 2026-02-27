<?php

namespace App\Models;

use App\Models\Accounts\Account;
use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Accounts\Contracts\Accountable;
use App\Services\Account\AccountSeederService;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CollectionOVA extends Model implements Accountable, Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $fillable = [
        'name',
        'partner_id',
        'balance',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
        self::created(function (CollectionOVA $ova) {
            AccountSeederService::addToFixedAccount($ova);
        });
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    public function general_ledger_account(): MorphOne
    {
        return $this->morphOne(Account::class, 'accountable');
    }

    public function accountDisplayName(): string
    {
        return $this->name;
    }

    public function fixedParentSlug(): string
    {
        return AccountSeederService::CASH_AT_MNO_FIXED_SLUG;
    }

    public function getIndentifier(): string
    {
        return "ACAM-C." . $this->id;
    }

    public function getTypeLetter(): string
    {
        return "A";
    }
}
