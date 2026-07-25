<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_name',
        'country',
        'division',
        'district',
        'upazila',
        'village',
        'id_number',
        'eiin_number',
        'mobile',
        'email',
        'logo',
        'sms_balance',
        'registration_number',
    ];

    // Relationship: School belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(SchoolSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(SchoolSubscription::class)
            ->where('status', 'active')
            ->latest();
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function adminSmsTemplates()
    {
        return $this->belongsToMany(AdminSmsTemplate::class, 'admin_sms_template_school');
    }

    public function adminSmsActivations()
    {
        return $this->hasMany(AdminSmsActivation::class);
    }
}
