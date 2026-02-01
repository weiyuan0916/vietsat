<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['name', 'duration_days', 'price', 'is_active'];
    protected $casts = [
        'price' => 'integer',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the orders for this service.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }
}

