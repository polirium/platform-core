<?php

namespace Polirium\Core\Base\Http\Models;

use Polirium\Core\Base\Http\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * User Dashboard Layout Model
 *
 * Stores per-user widget positions and visibility settings.
 */
class UserDashboardLayout extends BaseModel
{
    protected $table = 'user_dashboard_layouts';

    protected $fillable = [
        'uuid',
        'user_id',
        'widget_id',
        'position_x',
        'position_y',
        'width',
        'height',
        'order',
        'is_visible',
        'settings',
    ];

    protected $casts = [
        'position_x' => 'integer',
        'position_y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'order' => 'integer',
        'is_visible' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get the user that owns this layout
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get user's dashboard layout
     */
    public static function getLayoutForUser(int $userId): array
    {
        return static::where('user_id', $userId)
            ->where('is_visible', true)
            ->orderBy('order')
            ->get()
            ->values()
            ->toArray();
    }

    /**
     * Save user's dashboard layout
     */
    public static function saveLayoutForUser(int $userId, array $widgets): void
    {
        foreach ($widgets as $index => $widget) {
            static::updateOrCreate(
                [
                    'user_id' => $userId,
                    'widget_id' => $widget['id'],
                ],
                [
                    'position_x' => $widget['x'] ?? 0,
                    'position_y' => $widget['y'] ?? 0,
                    'width' => $widget['w'] ?? 4,
                    'height' => $widget['h'] ?? 2,
                    'order' => $index,
                    'is_visible' => $widget['visible'] ?? true,
                    'settings' => $widget['settings'] ?? null,
                ]
            );
        }
    }

    /**
     * Add widget to user's dashboard
     */
    public static function addWidgetForUser(int $userId, string $widgetId, array $config = []): static
    {
        $maxOrder = static::where('user_id', $userId)->max('order') ?? -1;

        return static::create([
            'user_id' => $userId,
            'widget_id' => $widgetId,
            'position_x' => $config['x'] ?? 0,
            'position_y' => $config['y'] ?? 0,
            'width' => $config['w'] ?? 4,
            'height' => $config['h'] ?? 2,
            'order' => $maxOrder + 1,
            'is_visible' => true,
            'settings' => $config['settings'] ?? null,
        ]);
    }

    /**
     * Remove widget from user's dashboard
     */
    public static function removeWidgetForUser(int $userId, string $widgetId): bool
    {
        return static::where('user_id', $userId)
            ->where('widget_id', $widgetId)
            ->delete() > 0;
    }
}
