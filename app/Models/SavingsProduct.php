<?php

namespace App\Models;

use App\Models\Partner;
use App\Models\SavingsAccount;
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

class SavingsProduct extends Model implements Accountable, Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $fillable = [
        'partner_id',
        'name',
        'description',
        'opening_balance',
        'minimum_balance',
        'current_balance',
        'previous_balance',
        'is_active',
        'active_status_changed_date',
        'interest_rate',
        'interest_payment_frequency',
        'interest_payment_computation_on',
        'code',
        'cost',
        'commission',
        'minimum_deposit',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
        // self::creating(function ($savings_product) {
        //     $id = strtoupper(uniqid());
        //     $savings_product->code = "SP-{$id}";
        // });
        // self::created(function (SavingsProduct $savings_product) {
        //     AccountSeederService::addToFixedAccount($savings_product);
        // });
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function accounts()
    {
        return $this->hasMany(SavingsAccount::class);
    }

    public function deposits()
    {
        return $this->through('accounts')->has('deposits');
    }

    public function withdraws()
    {
        return $this->through('accounts')->has('withdrawals');
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
        return "savings-products";
    }

    public function getIndentifier(): string
    {
        return "LSP." . $this->id;
    }

    public function getTypeLetter(): string
    {
        return "L";
    }
}
