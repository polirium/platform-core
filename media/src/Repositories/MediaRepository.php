<?php

namespace Polirium\Core\Media\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Polirium\Core\Media\Contracts\MediaRepositoryInterface;
use Polirium\Core\Media\Models\Media;

class MediaRepository implements MediaRepositoryInterface
{
    /**
     * @var Media
     */
    protected $model;

    /**
     * MediaRepository constructor.
     *
     * @param Media $model
     */
    public function __construct(Media $model)
    {
        $this->model = $model;
    }

    /**
     * Find media by ID.
     *
     * @param int $id
     * @return Media|null
     */
    public function findById(int $id): ?Media
    {
        return $this->model->find($id);
    }

    /**
     * Find media by collection.
     *
     * @param string $collection
     * @return Collection
     */
    public function findByCollection(string $collection): Collection
    {
        return $this->model->where('collection_name', $collection)->get();
    }

    /**
     * Find media by model.
     *
     * @param Model $model
     * @param string|null $collection
     * @return Collection
     */
    public function findByModel(Model $model, ?string $collection = null): Collection
    {
        $query = $this->model->where('model_type', get_class($model))
            ->where('model_id', $model->id);

        if ($collection) {
            $query->where('collection_name', $collection);
        }

        return $query->get();
    }

    /**
     * Search media.
     *
     * @param string $query
     * @param array $filters
     * @return Collection
     */
    public function search(string $query, array $filters = []): Collection
    {
        $builder = $this->model->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('file_name', 'like', "%{$query}%");
        });

        $builder = $this->applyFilters($builder, $filters);

        return $builder->get();
    }

    /**
     * Paginate media.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $builder = $this->model->query();
        $builder = $this->applyFilters($builder, $filters);

        return $builder->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create media.
     *
     * @param array $data
     * @return Media
     */
    public function create(array $data): Media
    {
        return $this->model->create($data);
    }

    /**
     * Update media.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $media = $this->findById($id);

        if (! $media) {
            return false;
        }

        return $media->update($data);
    }

    /**
     * Delete media.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $media = $this->findById($id);

        if (! $media) {
            return false;
        }

        return $media->delete();
    }

    /**
     * Delete multiple media.
     *
     * @param array $ids
     * @return int
     */
    public function deleteMultiple(array $ids): int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * Get all media.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Count media.
     *
     * @param array $filters
     * @return int
     */
    public function count(array $filters = []): int
    {
        $builder = $this->model->query();
        $builder = $this->applyFilters($builder, $filters);

        return $builder->count();
    }

    /**
     * Apply filters to query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyFilters($builder, array $filters)
    {
        if (isset($filters['collection'])) {
            $builder->where('collection_name', $filters['collection']);
        }

        if (isset($filters['mime_type'])) {
            $builder->where('mime_type', 'like', $filters['mime_type'] . '%');
        }

        if (isset($filters['type'])) {
            switch ($filters['type']) {
                case 'image':
                    $builder->images();

                    break;
                case 'video':
                    $builder->videos();

                    break;
                case 'document':
                    $builder->documents();

                    break;
                case 'audio':
                    $builder->audio();

                    break;
            }
        }

        if (isset($filters['model_type'])) {
            $builder->where('model_type', $filters['model_type']);
        }

        if (isset($filters['model_id'])) {
            $builder->where('model_id', $filters['model_id']);
        }

        if (isset($filters['from_date'])) {
            $builder->where('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $builder->where('created_at', '<=', $filters['to_date']);
        }

        return $builder;
    }
}
