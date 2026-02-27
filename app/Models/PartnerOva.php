<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartnerOva extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'app_name',
        'airtel_url',
        'airtel_public_key',
        'airtel_callback',
        'client_key',
        'client_secret',
        'pin',
    ];

    public static function rules(): array
    {
        return [
            "app_name" => "nullable|string|max:10",
            "airtel_url" => "nullable|string|max:255",
            "client_key" => ['nullable', 'required_with:client_secret', 'string', 'max:100'],
            "client_secret" => ['nullable', 'required_with:client_key', 'string', 'max:100'],
            "pin" => ['nullable', 'required_with:airtel_public_key', 'string', 'max:100'],
            "airtel_public_key" => ['nullable', 'required_with:pin', 'string', 'max:500'],
            "airtel_callback" => "nullable|string|max:255",
        ];
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public function partner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function airtelPublicKey(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                try {
                    return Crypt::decryptString($value);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    return $value;
                }
            },
            set: function ($value) {
                return Crypt::encryptString($value);
            },
        );
    }

    public function pin(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                try {
                    return Crypt::decryptString($value);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    return $value;
                }
            },
            set: function ($value) {
                return Crypt::encryptString($value);
            },
        );
    }

    public function clientSecret(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                try {
                    return Crypt::decryptString($value);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    return $value;
                }
            },
            set: function ($value) {
                return Crypt::encryptString($value);
            },
        );
    }
}
