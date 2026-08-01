<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attendable_id',
        'attendable_type',
        'device_id',
        'timestamp',
        'status',
        'type',
        'source',
    ];

    /**
     * Get the parent attendable model (student or teacher).
     */
    public function attendable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the device that recorded the attendance.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
