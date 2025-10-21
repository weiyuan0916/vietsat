<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Base Repository Interface
 * 
 * Defines common repository methods
 */
interface BaseRepositoryInterface
{
    /**
     * Get all records.
     */
    public function all(): Collection;

    /**
     * Find a record by ID.
     */
    public function find(int $id): ?Model;

    /**
     * Find a record by slug.
     */
    public function findBySlug(string $slug): ?Model;

    /**
     * Create a new record.
     */
    public function create(array $data): Model;

    /**
     * Update a record.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a record.
     */
    public function delete(int $id): bool;

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15);
}

