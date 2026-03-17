<?php

namespace App\Models;

use App\Services\Account\AccountSeederService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Partner extends Model implements Auditable
{
    use AuditingAuditable, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'Identification_Code',
        'Institution_Type',
        'Institution_Name',
        'License_Issuing_Date',
        'License_Number',
        'Telephone_Number',
        'Email_Address',
        'Access_Type',
        'Email_Notification_Recipients',
        'Accounting_Type',
    ];

    protected $appends = [
        'email',
        'name',
    ];

    // Create chart of accounts for partner once created
    protected static function boot(): void
    {
        parent::boot();
        self::created(function ($partner) {
            (new AccountSeederService($partner->id))->seedDefaultAccounts();
            CollectionOVA::create([
                'partner_id' => $partner->id,
                'name' => AccountSeederService::COLLECTION_OVA_NAME,
            ]);
            DisbursementOVA::create([
                'partner_id' => $partner->id,
                'name' => AccountSeederService::DISBURSEMENT_OVA_NAME,
            ]);
            BankAccount::create([
                'partner_id' => $partner->id,
                'name' => AccountSeederService::LOAN_OVA_ESCROW_BANK_ACCOUNT_NAME,
            ]);
        });
    }

    public static function generateCode(): string
    {
        $id = strtoupper(uniqid());
        $code = "GG-{$id}";
        $partner = Partner::where('Identification_Code', $code)->first();
        if ($partner) {
            throw new \Exception('Partner code already exists');
        }

        return $code;
    }

    public function switches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Switches::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function ova_setting(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PartnerOva::class);
    }

    public function ovas()
    {
        return $this->hasOne(PartnerOva::class);
    }

    public function api_setting()
    {
        return $this->hasOne(PartnerApiSetting::class);
    }

    public function api()
    {
        return $this->hasOne(PartnerApiSetting::class);
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class, 'partner_id');
    }

    public function assetProviderApiSettings()
    {
        return $this->hasMany(PartnerApiSetting::class)
            ->whereNotNull('api_name');
    }

    public function externalAccounts(): HasMany
    {
        return $this->hasMany(ExternalAccount::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(get: fn() => $this->Institution_Name);
    }

    public function email(): Attribute
    {
        return Attribute::make(get: fn() => $this->Email_Address);
    }

    public function hasLoansAccess(): bool
    {
        return strtolower($this->Access_Type) === 'loans';
    }

    // loan products
    public function loan_products(): HasMany
    {
        return $this->hasMany(LoanProduct::class, 'partner_id');
    }

    // saving products
    public function saving_products(): HasMany
    {
        return $this->hasMany(SavingsProduct::class, 'partner_id');
    }

    public function blacklistedCustomers()
    {
        return $this->belongsToMany(Customer::class, 'blacklisted_customers')
            ->withPivot('reason')
            ->withTimestamps();
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'partner_id');
    }

    public function smsPrice()
    {
        return $this->sms_price;
    }


    public function smsCost()
    {
        return config('lms.sms.cost');
    }
}
