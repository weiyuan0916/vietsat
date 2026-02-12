<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ServiceOrder extends Model
{
    protected $fillable = [
        'order_code',
        'service_id',
        'amount',
        'status',
        'expires_at',
        'paid_at',
        'bank_txn_id',
        'facebook_profile_link',
    ];

    protected $casts = [
        'amount' => 'integer',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_EXPIRED = 'expired';

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
}
