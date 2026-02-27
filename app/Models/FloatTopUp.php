<?php

namespace App\Models;

use App\Models\Accounts\Account;
use App\Models\Scopes\PartnerScope;
use App\Models\Transactables\BaseTransaction;
use OwenIt\Auditing\Contracts\Auditable;
use App\Services\Account\AccountSeederService;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use App\Models\Transactables\Contracts\Transactable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FloatTopUp extends BaseTransaction implements Auditable, Transactable
{
    use HasFactory, SoftDeletes, AuditingAuditable;

    protected $fillable = [
        'partner_id',
        'Amount',
        'Proof_Of_Payment',
        'Status'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function amount()
    {
        return $this->Amount;
    }

    protected function makeJournalEntries(): void
    {
        $debit_account_Disbursement_OVA_ASSET = Account::where('partner_id', $this->partner_id)
            ->where('slug', AccountSeederService::DISBURSEMENT_OVA_SLUG)
            ->first();

        $this->transactions[] = JournalEntry::make(
            'debit',
            $this->partner_id,
            null,
            $debit_account_Disbursement_OVA_ASSET->id,
            $debit_account_Disbursement_OVA_ASSET->name,
            $this->amount(),
            'Non Cash',
        );

        $credit_account_Disbursement_OVA_CAPITAL = Account::where('partner_id', $this->partner_id)
            ->where('slug', AccountSeederService::LOAN_OVA_ESCROW_BANK_ACCOUNT_SLUG)
            ->first();

        $this->transactions[] = JournalEntry::make(
            'credit',
            $this->partner_id,
            null,
            $credit_account_Disbursement_OVA_CAPITAL->id,
            $credit_account_Disbursement_OVA_CAPITAL->name,
            $this->amount(),
            'Non Cash',
        );
    }
}
