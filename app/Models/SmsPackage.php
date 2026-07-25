<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsPackage extends Model
{
    protected $fillable = [
        'name',
        'sms_quantity',
        'validity_days',
        'purchase_price',
        'sale_price',
        'profit_per_package',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->profit_per_package =
                $model->sale_price - $model->purchase_price;
        });
    }

    public function purchases()
    {
        return $this->hasMany(SmsPackagePurchase::class);
    }
}
