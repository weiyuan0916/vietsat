<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'service_id',
        'user_id',
        'service_data',
        'amount',
        'status',
        'expires_at',
        'paid_at',
        'bank_txn_id',
        'facebook_profile_link',
        'device_fingerprint',
        'ip_address',
        'user_agent',
        'processing_started_at',
        'processing_completed_at',
        'extension_result',
    ];

    protected $casts = [
        'amount' => 'integer',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'service_data' => 'array',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_PROCESSING = 'processing';

    /**
     * Generate a unique order code.
     */
    public static function generateOrderCode(): string
    {
        return 'ORD-' . strtoupper(Str::random(10));
    }

    /**
     * Get the service that owns this order.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the user that owns this order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if order is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    /**
     * Check if order is expired based on expires_at.
     */
    public function isTimeExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get service information for this order.
     * Returns data from service_data if available (external API),
     * otherwise falls back to local service relationship.
     *
     * @return array|null
     */
    public function getServiceInfo(): ?array
    {
        // If we have external service data, use it
        if ($this->service_data) {
            return [
                'id' => $this->service_data['id'] ?? null,
                'name' => $this->service_data['name'] ?? null,
                'duration_days' => $this->service_data['duration_days'] ?? null,
            ];
        }

        // Otherwise, try to get from local service
        if ($this->service) {
            return [
                'id' => $this->service->id,
                'name' => $this->service->name,
                'duration_days' => $this->service->duration_days,
            ];
        }

        return null;
    }

    /**
     * Check if this order uses external service data.
     *
     * @return bool
     */
    public function hasExternalServiceData(): bool
    {
        return !empty($this->service_data);
    }

    public function markExpired(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->status = self::STATUS_EXPIRED;
        $this->save();

        return true;
    }
}
