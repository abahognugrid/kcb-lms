<?php

namespace App\Models;

use App\Enums\LoanAccountType;
use App\Models\LoanRepayment;
use App\Models\LoanDisbursement;
use App\Models\Scopes\BarnScope;
use App\Notifications\SmsNotification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Rules\ValidPhoneNumber;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class Customer extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'First_Name',
        'Last_Name',
        'Other_Name',
        'Gender',
        'Marital_Status',
        'Date_of_Birth',
        'ID_Type',
        'ID_Number',
        'Telephone_Number',
        'Email_Address',
        'Classification',
        'options',
    ];

    public const ID_TYPES = [
        'Country_ID',
        'Passport_Number',
        'Refugee_Number',
    ];

    protected function casts(): array
    {
        return [
            'Date_of_Birth' => 'date',
            'options' => 'array',
        ];
    }

    protected static function booted()
    {
        static::addGlobalScope(new BarnScope); // You can barn a customer.
        static::created(function ($customer) {
            $partner = Partner::first();
            $customer->notify(
                new SmsNotification(
                    'Dear ' . $customer->name . ', your KCB Agent Loan account has been created successfully. Contact KCB for any assistance.',
                    $customer->Telephone_Number,
                    $customer->id,
                    $partner->id,
                    $partner->smsPrice(),
                    $partner->smsCost(),
                )
            );
            $customer->storeCreditLimit();
        });
    }

    public static function rules($customer = null)
    {
        $customer_email_rule = [];
        if ($customer) {
            $customer_email_rule = ['email_address' => 'nullable|string|max:100|unique:customers,Email_Address,' . $customer->id];
        } else {
            $customer_email_rule = ['email_address' => 'required|string|max:100|unique:customers,Email_Address'];
        }
        return [
            "first_name" => "required|string|max:100",
            "last_name" => "required|string|max:100",
            "other_name" => "nullable|string|max:100",
            "gender" => "required|string|max:100|in:Male,Female",
            "marital_status" => "sometimes|string|max:100|in:Single (never married),Married,Divorced,Widowed,Separated,Annulled,Cohabitating,Other",
            "date_of_birth" => "required|date|before:today",
            "id_type" => ['required', 'string', Rule::in(self::ID_TYPES)],
            "id_number" => "required|string|max:100",
            "classification" => "required|string|max:100|in:Individual,Non-Indvidual",
            "telephone_number" => ['required', new ValidPhoneNumber],
        ] + $customer_email_rule;
    }

    public function getNameAttribute()
    {
        return $this->First_Name . ' ' . $this->Last_Name;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'Telephone_Number', 'Telephone_Number');
    }

    public function loans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Loan::class, 'Customer_ID');
    }

    public function activeLoans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->loans()
            ->whereNotIn('Credit_Account_Status', [
                LoanAccountType::PaidOff->value,
                LoanAccountType::WrittenOff->value,
            ])->latest();
    }

    public function unpaidLoan()
    {
        return $this
            ->loans()
            ->whereHas('latestOutstandingPayment');
    }

    public function loan_disbursements()
    {
        return $this->hasMany(LoanDisbursement::class, 'customer_id');
    }

    public function loan_repayments()
    {
        return $this->hasMany(LoanRepayment::class, 'Customer_ID');
    }

    public function journal_entries()
    {
        return $this->hasMany(JournalEntry::class, 'customer_id');
    }
    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class, 'Customer_ID');
    }
    public function validations()
    {
        return $this->morphMany(Validation::class, 'validateable');
    }

    public function barn($reason = null)
    {
        $this->IS_Barned = true;
        $this->Barning_Reason = $reason;
        $this->save();
    }

    public function unbarn()
    {
        $this->IS_Barned = false;
        $this->Barning_Reason = null; // Todo MA. Do this?
        $this->save();
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $fullName = $this->Last_Name . ' ' . $this->First_Name;

                if (! is_null($this->Other_Name)) {
                    return  $fullName .= " {$this->Other_Name}";
                }

                return $fullName;
            }
        );
    }

    public function routeNotificationForSms()
    {
        return $this->Telephone_Number; // Return the phone number for SMS notifications
    }

    public function optedOutAt(): Attribute
    {
        return Attribute::make(
            get: function () {
                return data_get($this->options, 'opt_out_at');
            }
        );
    }

    public function getCreditScores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CreditScore::class, 'customerId')
            ->orderBy('created_at', 'desc');
    }

    public function accountNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                $savingAccountNumber = '1';

                if (str($this->id)->length() < 10) {
                    $savingAccountNumber .= str($this->id)->padLeft(10, '0');
                } else {
                    $savingAccountNumber = $this->id;
                }

                return $savingAccountNumber;
            }
        );
    }

    public function loanApplicationAccountNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                return data_get($this->options, 'loanaccounts.loanaccount.accountnumber');
            }
        );
    }

    public function blacklistedByPartners()
    {
        return $this->belongsToMany(Partner::class, 'blacklisted_customers')
            ->withPivot('reason')
            ->withTimestamps();
    }
    public function isBlacklistedByPartner($partnerId)
    {
        return $this->blacklistedByPartners()->where('partner_id', $partnerId)->exists();
    }

    public function creditLimits()
    {
        return $this->hasMany(CreditLimit::class, 'customer_id');
    }

    protected function getAccessToken(): string
    {
        $accessToken = Cache::get('api_access_token');
        if ($accessToken) {
            return $accessToken;
        } else {
            // Step 1: Get the access token
            $tokenResponse = Http::asForm()->post(config('lms.crb.url') . '/v1/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('lms.crb.client-id'),
                'client_secret' => config('lms.crb.client-secret'),
            ]);
            if ($tokenResponse->successful()) {
                $accessToken = $tokenResponse->json()['access_token'];
                $tokenLifeTime = $tokenResponse->json()['expires_in']; // seconds
                Cache::put('api_access_token', $accessToken, $tokenLifeTime);
                return $accessToken;
            } else {
                Log::error('Failed to retrieve access token: ' . $tokenResponse->body());
                throw new Exception('Failed to get access token');
            }
        }
    }

    private function storeCreditLimit()
    {
        try {
            $accessToken = $this->getAccessToken();
            // Step 2: Use the access token to call the Loan Market API
            $apiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post(config('lms.crb.url') . '/v1/credit-enquiries/credit-limits', [
                'phone_number' => $this->Telephone_Number,
                'entity_type' => 0,
                'client_consented' => 'Yes'
            ]);

            if ($apiResponse->successful()) {
                Log::info('API call successful: ' . $apiResponse->body());
                $responseData = $apiResponse->json();
                $creditLimit = data_get($responseData, 'data.Decision.credit_limit');
                $partner = Partner::first();

                CreditLimit::create([
                    'customer_id' => $this->id,
                    'partner_id' => $partner->id,
                    'credit_limit' => $creditLimit,
                    'used_credit' => 0,
                    'available_credit' => $creditLimit,
                    'Created_At' => Carbon::now(),
                    'Updated_At' => Carbon::now(),
                ]);
                $customer = Customer::find($this->id);
                $customer->notify(
                    new SmsNotification(
                        'Dear ' . $customer->name . ', welcome to KCB Agent Loan. Your credit limit is UGX ' . number_format($creditLimit) . '. You can borrow up to this amount anytime. Contact KCB for assistance.',
                        $customer->Telephone_Number,
                        $customer->id,
                        $partner->id,
                        $partner->smsPrice(),
                        $partner->smsCost(),
                    )
                );
            } else {
                Log::error('API call failed: ' . $apiResponse->body());
            }

            return 0;
        } catch (Exception $e) {
            Log::error('Error caught: ' . $e->getMessage());
        }
    }
}
