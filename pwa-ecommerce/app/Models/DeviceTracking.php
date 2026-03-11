<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceTracking extends Model
{
    protected $fillable = [
        'device_fingerprint',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'os',
    ];

    /**
     * Find or create device tracking record.
     */
    public static function findOrCreate(string $fingerprint, array $data = []): DeviceTracking
    {
        return static::firstOrCreate(
            ['device_fingerprint' => $fingerprint],
            $data
        );
    }

    /**
     * Check if IP matches.
     */
    public function matchesIp(string $ip): bool
    {
        return $this->ip_address === $ip;
    }
}

