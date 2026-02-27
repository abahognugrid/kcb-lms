<?php

namespace App\Models;

use App\Models\Scopes\PartnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = ['Message', 'Customer_ID', 'partner_id', 'Category', 'Telephone_Number', 'Status'];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Customer_ID', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PartnerScope);
    }

    public static function store($Message, $partner_id, $Customer_ID, $Category, $Telephone_Number)
    {
        SmsLog::updateOrCreate(
            [
                "partner_id" => $partner_id,
                "Customer_ID" => $Customer_ID,
                "Telephone_Number" => $Telephone_Number,
                "Category" => $Category,
                'created_at' => now(),
            ],
            [
                "Message" => $Message,
            ]
        );
    }
}
