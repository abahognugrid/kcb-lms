<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transactables\SavingsDeposit;
use App\Models\Transactables\SavingsWithdraw;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingsAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'customer_id',
        'savings_product_id',
        'expected_amount',
        'current_balance',
        'previous_balance',
        'opening_balance',
        'minimum_balance',
        'is_active',
        'last_deposit_date',
        'last_withdraw_date',
        'active_status_changed_date',
    ];

    protected function casts(): array
    {
        return [
            'last_deposit_date' => 'datetime',
            'last_withdraw_date' => 'datetime',
            'active_status_changed_date' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function savings_product()
    {
        return $this->belongsTo(SavingsProduct::class, 'savings_product_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}
