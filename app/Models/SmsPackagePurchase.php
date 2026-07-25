<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsPackagePurchase extends Model
{
    protected $fillable = [
        'trx_id',
        'school_id',
        'sms_package_id',
        'total_sms',
        'used_sms',
        'available_sms',
        'purchase_price',
        'sale_price',
        'profit',
        'status',
        'payment_method',
        'purchase_date',
        'expiry_date',
        'admin_note'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function package()
    {
        return $this->belongsTo(SmsPackage::class, 'sms_package_id');
    }
}
