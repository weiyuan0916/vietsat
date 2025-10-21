<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * License Activation Model
 * 
 * Tracks machine activations for licenses.
 */
class LicenseActivation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'license_id',
        'machine_id',
        'machine_name',
        'ip_address',
        'hardware_info',
        'status',
        'activated_at',
        'last_validated_at',
        'deactivated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hardware_info' => 'array',
        'activated_at' => 'datetime',
        'last_validated_at' => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    /**
     * Get the license that owns this activation.
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    /**
     * Check if activation is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Deactivate this activation.
     */
    public function deactivate(): bool
    {
        $this->status = 'deactivated';
        $this->deactivated_at = now();
        
        return $this->save();
    }

    /**
     * Update last validation timestamp.
     */
    public function updateValidation(): bool
    {
        $this->last_validated_at = now();
        
        return $this->save();
    }

    /**
     * Scope query to only active activations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope query by machine ID.
     */
    public function scopeByMachine($query, string $machineId)
    {
        return $query->where('machine_id', $machineId);
    }
}

