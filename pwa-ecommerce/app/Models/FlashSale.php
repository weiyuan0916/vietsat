<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * FlashSale Model
 * 
 * Represents a flash sale for a product
 */
class FlashSale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'title',
        'flash_price',
        'quantity_available',
        'quantity_sold',
        'sold_percentage',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'flash_price' => 'decimal:2',
        'sold_percentage' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product that owns the flash sale.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if flash sale is currently active.
     */
    public function isActive(): bool
    {
        return $this->is_active 
            && now()->between($this->starts_at, $this->ends_at)
            && ($this->quantity_available === null || $this->quantity_sold < $this->quantity_available);
    }

    /**
     * Scope a query to only include active flash sales.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    /**
     * Scope a query to only include ongoing flash sales.
     */
    public function scopeOngoing($query)
    {
        return $query->active()
            ->where(function($q) {
                $q->whereNull('quantity_available')
                  ->orWhereRaw('quantity_sold < quantity_available');
            });
    }
}
