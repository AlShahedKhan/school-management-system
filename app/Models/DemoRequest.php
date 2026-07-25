<?php

namespace App\Models;

use App\Enums\DemoRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_name',
        'phone',
        'student_qty',
        'booking_date',
        'booking_time',
        'email',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => DemoRequestStatus::class,
            'student_qty' => 'integer',
            'booking_date' => 'date',
        ];
    }
}
