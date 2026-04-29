<?php

namespace Polirium\Core\Base\Widgets;

use Livewire\Component;

/**
 * Abstract Widget Base Class
 *
 * All dashboard widgets should extend this class.
 * Provides common functionality for widget identification and rendering.
 */
abstract class AbstractWidget extends Component
{
    /**
     * Widget width in grid units (1-12)
     */
    public int $width = 4;

    /**
     * Widget height in grid units
     */
    public int $height = 2;

    /**
     * Widget position X
     */
    public int $x = 0;

    /**
     * Widget position Y
     */
    public int $y = 0;

    /**
     * Get the unique widget ID
     */
    abstract public static function getWidgetId(): string;

    /**
     * Get the widget display name
     */
    abstract public static function getWidgetName(): string;

    /**
     * Get the widget icon (Tabler icon name)
     */
    public static function getIcon(): string
    {
        return 'layout-dashboard';
    }

    /**
     * Get widget description
     */
    public static function getDescription(): string
    {
        return '';
    }

    /**
     * Get default width
     */
    public static function getDefaultWidth(): int
    {
        return 4;
    }

    /**
     * Get default height
     */
    public static function getDefaultHeight(): int
    {
        return 2;
    }

    /**
     * Get required permissions to view this widget
     */
    public static function getPermissions(): array
    {
        return [];
    }

    /**
     * Auto-register widget to registry
     */
    public static function register(): void
    {
        app('dashboard.widgets')->register(static::getWidgetId(), [
            'name' => static::getWidgetName(),
            'component' => static::getComponentName(),
            'icon' => static::getIcon(),
            'description' => static::getDescription(),
            'default_width' => static::getDefaultWidth(),
            'default_height' => static::getDefaultHeight(),
            'permissions' => static::getPermissions(),
        ]);
    }

    /**
     * Get the Livewire component name
     */
    protected static function getComponentName(): string
    {
        // Override this in child classes
        return '';
    }
}
