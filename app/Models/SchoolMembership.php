<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'member_no',
        'name',
        'address',
        'mobile_number',
        'income_source',
        'amount'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
