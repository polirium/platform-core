<?php

namespace Polirium\Core\Base\Http\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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
        return $this->belongsTo(User::class)->withoutGlobalScope(\Illuminate\Database\Eloquent\SoftDeletingScope::class);
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
     * Whether the user has ever saved a layout (rows exist, visible or not).
     */
    public static function hasLayout(int $userId): bool
    {
        return static::where('user_id', $userId)->exists();
    }

    /**
     * Save user's dashboard layout. Widgets not in it are kept as hidden rows,
     * so an emptied dashboard stays empty and removed widgets stay removed.
     *
     * @param list<string> $hiddenWidgetIds other widgets the user could add
     */
    public static function saveLayoutForUser(int $userId, array $widgets, array $hiddenWidgetIds = []): void
    {
        DB::transaction(function () use ($userId, $widgets, $hiddenWidgetIds): void {
            $savedWidgetIds = [];

            foreach (array_values($widgets) as $index => $widget) {
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
                        'is_visible' => true,
                        'settings' => $widget['settings'] ?? null,
                    ]
                );

                $savedWidgetIds[] = $widget['id'];
            }

            // Hide widgets that were removed by the user
            static::where('user_id', $userId)
                ->whereNotIn('widget_id', $savedWidgetIds)
                ->update(['is_visible' => false]);

            foreach (array_diff($hiddenWidgetIds, $savedWidgetIds) as $widgetId) {
                static::createOrFirst(
                    ['user_id' => $userId, 'widget_id' => $widgetId],
                    ['is_visible' => false, 'order' => 0]
                );
            }
        });
    }

    /**
     * Add widget to user's dashboard once: createOrFirst relies on the unique
     * (user_id, widget_id) key, so two tabs loading together do not fail.
     */
    public static function addWidgetForUser(int $userId, string $widgetId, array $config = []): static
    {
        $maxOrder = static::where('user_id', $userId)->max('order') ?? -1;

        return static::createOrFirst(
            ['user_id' => $userId, 'widget_id' => $widgetId],
            [
                'position_x' => $config['x'] ?? 0,
                'position_y' => $config['y'] ?? 0,
                'width' => $config['w'] ?? 4,
                'height' => $config['h'] ?? 2,
                'order' => $maxOrder + 1,
                'is_visible' => true,
                'settings' => $config['settings'] ?? null,
            ]
        );
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

    /**
     * Check if a widget record exists for user (including hidden)
     */
    public static function hasWidgetRecord(int $userId, string $widgetId): bool
    {
        return static::where('user_id', $userId)
            ->where('widget_id', $widgetId)
            ->exists();
    }
}
