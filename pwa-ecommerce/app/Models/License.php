<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * License Model
 * 
 * Manages software license keys with expiration and activation tracking.
 */
class License extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'license_key',
        'type',
        'status',
        'max_activations',
        'current_activations',
        'issued_at',
        'expires_at',
        'last_renewed_at',
        'metadata',
        'min_app_version',
        'latest_app_version',
        'force_update',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_renewed_at' => 'datetime',
        'metadata' => 'array',
        'max_activations' => 'integer',
        'current_activations' => 'integer',
        'force_update' => 'boolean',
    ];

    /**
     * Get all activations for this license.
     */
    public function activations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class);
    }

    /**
     * Get active activations only.
     */
    public function activeActivations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class)
            ->where('status', 'active');
    }

    /**
     * Check if license is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if license is valid (active and not expired).
     */
    public function isValid(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Check if license can accept more activations.
     */
    public function canActivate(): bool
    {
        return $this->current_activations < $this->max_activations;
    }

    /**
     * Get days remaining until expiration.
     */
    public function daysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at);
    }

    /**
     * Generate a new license key.
     */
    public static function generateKey(string $prefix = 'LS'): string
    {
        $segments = [
            $prefix,
            strtoupper(Str::random(4)),
            strtoupper(Str::random(4)),
            strtoupper(Str::random(4)),
            strtoupper(Str::random(4)),
        ];

        return implode('-', $segments);
    }

    /**
     * Scope query to only active licenses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope query to only expired licenses.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope query to only valid licenses (active and not expired).
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    /**
     * Check if the app version meets minimum requirements.
     *
     * @param string $appVersion Current app version (e.g., "1.2.3")
     * @return bool
     */
    public function isVersionCompatible(string $appVersion): bool
    {
        if (!$this->min_app_version) {
            return true; // No version requirement
        }

        return version_compare($appVersion, $this->min_app_version, '>=');
    }

    /**
     * Check if an update is available.
     *
     * @param string $appVersion Current app version
     * @return bool
     */
    public function hasUpdateAvailable(string $appVersion): bool
    {
        if (!$this->latest_app_version) {
            return false;
        }

        return version_compare($appVersion, $this->latest_app_version, '<');
    }

    /**
     * Check if user must update before using the app.
     *
     * @param string $appVersion Current app version
     * @return bool
     */
    public function requiresUpdate(string $appVersion): bool
    {
        return $this->force_update && !$this->isVersionCompatible($appVersion);
    }

    /**
     * Get version status information.
     *
     * @param string $appVersion Current app version
     * @return array
     */
    public function getVersionStatus(string $appVersion): array
    {
        return [
            'current_version' => $appVersion,
            'min_version' => $this->min_app_version,
            'latest_version' => $this->latest_app_version,
            'is_compatible' => $this->isVersionCompatible($appVersion),
            'has_update' => $this->hasUpdateAvailable($appVersion),
            'requires_update' => $this->requiresUpdate($appVersion),
            'force_update' => $this->force_update ?? false,
        ];
    }
}

