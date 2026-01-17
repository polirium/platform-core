<?php

namespace Polirium\Core\Base\Http\Models;

use Polirium\Core\Base\Http\Models\BaseModel;

class Module extends BaseModel
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'version',
        'namespace',
        'provider',
        'path',
        'status',
        'dependencies',
        'author',
        'image',
    ];

    protected $casts = [
        'dependencies' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_INSTALLED = 'installed';
    const STATUS_ACTIVE = 'active';
    const STATUS_DISABLED = 'disabled';

    /**
     * Scope: Get active modules
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Get pending modules (discovered but not installed)
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Get installed modules (installed but not active)
     */
    public function scopeInstalled($query)
    {
        return $query->where('status', self::STATUS_INSTALLED);
    }

    /**
     * Scope: Get disabled modules
     */
    public function scopeDisabled($query)
    {
        return $query->where('status', self::STATUS_DISABLED);
    }

    /**
     * Check if module is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if module is installed
     */
    public function isInstalled(): bool
    {
        return in_array($this->status, [self::STATUS_INSTALLED, self::STATUS_ACTIVE, self::STATUS_DISABLED]);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INSTALLED => 'info',
            self::STATUS_DISABLED => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => __('Đang hoạt động'),
            self::STATUS_INSTALLED => __('Đã cài đặt'),
            self::STATUS_DISABLED => __('Đã tắt'),
            default => __('Chờ cài đặt'),
        };
    }
}
