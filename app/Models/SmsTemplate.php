<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    public function loanProduct()
    {
        return $this->belongsTo(LoanProduct::class, 'Loan_Product_ID', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }
}
