<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'name',
        'model',
        'serial_number',
        'ip_address',
        'port',
        'communication_type',
        'protocol',
        'time_zone',
        'sync_interval',
        'heartbeat_time',
        'connection_timeout',
        'firmware_version',
        'device_password',
        'status',
    ];

    /**
     * Get the school that owns the device.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
