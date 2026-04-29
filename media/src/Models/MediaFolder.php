<?php

namespace Polirium\Core\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaFolder extends Model
{
    use SoftDeletes;

    protected $table = 'media_folders';

    protected $fillable = [
        'name',
        'path',
        'parent_id',
    ];

    /**
     * Get the parent folder.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }

    /**
     * Get child folders.
     */
    public function children(): HasMany
    {
        return $this->hasMany(MediaFolder::class, 'parent_id');
    }

    /**
     * Get all descendant folders (recursive).
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get files in this folder.
     */
    public function files(): HasMany
    {
        return $this->hasMany(Media::class, 'collection_name', 'path');
    }

    /**
     * Get the physical storage path.
     */
    public function getStoragePath(): string
    {
        $disk = config('media.default_disk', 'public');

        return \Storage::disk($disk)->path($this->path);
    }

    /**
     * Check if physical directory exists.
     */
    public function physicalExists(): bool
    {
        $disk = config('media.default_disk', 'public');

        return \Storage::disk($disk)->exists($this->path);
    }

    /**
     * Create physical directory if not exists.
     */
    public function ensurePhysicalExists(): void
    {
        $disk = config('media.default_disk', 'public');
        if (! $this->physicalExists()) {
            \Storage::disk($disk)->makeDirectory($this->path);
        }
    }

    /**
     * Scope to get root folders (under uploads).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get folders at a specific path level.
     */
    public function scopeAtPath($query, string $parentPath)
    {
        if (empty($parentPath) || $parentPath === 'uploads') {
            return $query->root();
        }

        $parent = static::where('path', $parentPath)->first();
        if ($parent) {
            return $query->where('parent_id', $parent->id);
        }

        return $query->whereRaw('1 = 0'); // No results
    }
}
