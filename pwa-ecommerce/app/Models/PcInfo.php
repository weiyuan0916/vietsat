<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PcInfo Model
 *
 * Represents PC/client information for tracking connected devices
 */
class PcInfo extends Model
{
    /** @use HasFactory<\Database\Factories\PcInfoFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'host_name',
        'user_name',
        'password',
        'local_ip_address',
        'public_ip_address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the display name for the PC info.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->host_name ?: ($this->user_name ?: 'Unknown PC');
    }

    /**
     * Get the full IP information.
     */
    public function getFullIpInfoAttribute(): string
    {
        $info = [];
        if ($this->local_ip_address) {
            $info[] = "Local: {$this->local_ip_address}";
        }
        if ($this->public_ip_address) {
            $info[] = "Public: {$this->public_ip_address}";
        }
        return implode(' | ', $info) ?: 'No IP info';
    }

    /**
     * Scope a query to filter by host name.
     */
    public function scopeByHostName($query, string $hostName)
    {
        return $query->where('host_name', 'like', "%{$hostName}%");
    }

    /**
     * Scope a query to filter by IP address.
     */
    public function scopeByIpAddress($query, string $ipAddress)
    {
        return $query->where(function ($q) use ($ipAddress) {
            $q->where('local_ip_address', 'like', "%{$ipAddress}%")
              ->orWhere('public_ip_address', 'like', "%{$ipAddress}%");
        });
    }

    /**
     * Scope a query to filter by user name.
     */
    public function scopeByUserName($query, string $userName)
    {
        return $query->where('user_name', 'like', "%{$userName}%");
    }
}









