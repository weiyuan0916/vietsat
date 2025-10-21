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
}

