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
        'update_file_path',
        'update_file_version',
        'update_file_size',
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
        'update_file_size' => 'integer',
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
        $status = [
            'current_version' => $appVersion,
            'min_version' => $this->min_app_version,
            'latest_version' => $this->latest_app_version,
            'is_compatible' => $this->isVersionCompatible($appVersion),
            'has_update' => $this->hasUpdateAvailable($appVersion),
            'requires_update' => $this->requiresUpdate($appVersion),
            'force_update' => $this->force_update ?? false,
        ];

        // Include download URL if update file is available
        if ($this->update_file_path) {
            $status['download_url'] = $this->getUpdateFileUrl();
            $status['file_version'] = $this->update_file_version;
            $status['file_size'] = $this->update_file_size;
            $status['file_size_formatted'] = $this->getFormattedFileSize();
        }

        return $status;
    }

    /**
     * Get the public URL for the update file.
     *
     * @return string|null
     */
    public function getUpdateFileUrl(): ?string
    {
        if (!$this->update_file_path) {
            return null;
        }

        return url('/api/v1/licenses/download-update/' . $this->license_key);
    }

    /**
     * Get formatted file size (e.g., "15.5 MB").
     *
     * @return string|null
     */
    public function getFormattedFileSize(): ?string
    {
        if (!$this->update_file_size) {
            return null;
        }

        $bytes = $this->update_file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the absolute file path for the update file.
     *
     * @return string|null
     */
    public function getUpdateFilePath(): ?string
    {
        if (!$this->update_file_path) {
            return null;
        }

        return storage_path('app/' . $this->update_file_path);
    }

    /**
     * Check if update file exists.
     *
     * @return bool
     */
    public function hasUpdateFile(): bool
    {
        return $this->update_file_path && file_exists($this->getUpdateFilePath());
    }
}

