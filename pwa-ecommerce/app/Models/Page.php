<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get page by slug.
     */
    public static function getBySlug(string $slug): ?Page
    {
        return static::where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }
}

