<?php

namespace Polirium\Core\Media\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Polirium\Core\Media\Models\Media;

interface MediaRepositoryInterface
{
    /**
     * Find media by ID.
     *
     * @param int $id
     * @return Media|null
     */
    public function findById(int $id): ?Media;

    /**
     * Find media by collection.
     *
     * @param string $collection
     * @return Collection
     */
    public function findByCollection(string $collection): Collection;

    /**
     * Find media by model.
     *
     * @param Model $model
     * @param string|null $collection
     * @return Collection
     */
    public function findByModel(Model $model, ?string $collection = null): Collection;

    /**
     * Search media.
     *
     * @param string $query
     * @param array $filters
     * @return Collection
     */
    public function search(string $query, array $filters = []): Collection;

    /**
     * Paginate media.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Create media.
     *
     * @param array $data
     * @return Media
     */
    public function create(array $data): Media;

    /**
     * Update media.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete media.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Delete multiple media.
     *
     * @param array $ids
     * @return int
     */
    public function deleteMultiple(array $ids): int;

    /**
     * Get all media.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Count media.
     *
     * @param array $filters
     * @return int
     */
    public function count(array $filters = []): int;
}
