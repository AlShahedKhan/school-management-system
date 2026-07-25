<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'relation',
        'division',
        'district',
        'upazila',
        'village',
        'mobile',
    ];

    // Relationship: Guardian has many admission students
    public function students()
    {
        return $this->hasMany(AdmissionStudent::class);
    }
}
