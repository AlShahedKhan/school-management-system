<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DeviceStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'last_heartbeat_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => DeviceStatus::class,
        'port' => 'integer',
        'sync_interval' => 'integer',
        'heartbeat_time' => 'integer',
        'connection_timeout' => 'integer',
        'last_heartbeat_at' => 'datetime',
    ];

    /**
     * Get the school that owns the device.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Determine the device's connection status based on the last heartbeat.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function connectionStatus(): Attribute
    {
        return Attribute::make(
            get: function () {

                if (!$this->last_heartbeat_at || $this->last_heartbeat_at->diffInMinutes(now()) > 5) {
                    return ['status' => 'Offline', 'color' => 'danger'];
                }

                return ['status' => 'Online', 'color' => 'success'];
            }
        );
    }


    /**
     * Get the appropriate icon for the communication type.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function communicationIcon(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->communication_type === 'WiFi'
                ? 'bi-wifi'
                : 'bi-router'
        );
    }
}

