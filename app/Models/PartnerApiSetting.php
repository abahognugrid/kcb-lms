<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $partner_id
 * @property string $api_key
 * @property string $refresh_token
 * @property string $api_name
 * @property string $api_scopes
 * @property string $expires_at
 * @property string $has_been_used
 * @property string $last_used_at
 */
class PartnerApiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'api_key',
        'api_name',
        'api_scopes',
        'expires_at', // Set to null means it doesn't expire
        'has_been_used',
        'last_used_at',
        'refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
            'api_scopes' => 'array',
        ];
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
        self::creating(function ($model) {
            if (empty($model->api_key)) {
                /**
                 * Create key only if we did not intentionally pass one
                 */
                $model->api_key = Str::random(32);
            }
        });
    }

    public function partner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public static function rules(): array
    {
        return [
            'expires_at' => 'nullable|date', // Set to null means it doesn't expire
            'api_scopes' => 'nullable:json',
        ];
    }

    /**
     * Determine if the API key has expired
     *
     * Set to null means it doesn't expire
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if ($this->expires_at) {
            return now()->greaterThan($this->expires_at);
        }

        return false;
    }

    public function apiKey(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                try {
                    return decrypt($value);
                } catch (\Exception $e) {
                    return $value;
                }
            },
            set: function ($value) {

                // Use encryptString to encrypt without serialization
                return encrypt($value);
            },
        );
    }

    public function refreshToken(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                try {
                    return decrypt($value);
                } catch (\Exception $e) {
                    return $value;
                }
            },
            set: function ($value) {
                return encrypt($value);
            },
        );
    }
}
