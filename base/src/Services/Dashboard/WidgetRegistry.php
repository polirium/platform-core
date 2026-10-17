<?php

namespace Polirium\Core\Base\Services\Dashboard;

/**
 * Widget Registry Service
 *
 * Manages dynamic widget registration from modules.
 * Widgets can be registered in ServiceProvider boot method.
 *
 * Example usage:
 * app('dashboard.widgets')->register('my-widget', [
 *     'name' => 'My Widget',
 *     'component' => 'core/base::widgets.my-widget',
 *     'icon' => 'chart-bar',
 *     'default_width' => 4,
 *     'default_height' => 2,
 * ]);
 */
class WidgetRegistry
{
    /**
     * Registered widgets
     */
    protected array $widgets = [];

    /**
     * Register a new widget
     */
    public function register(string $id, array $config): self
    {
        $this->widgets[$id] = array_merge([
            'id' => $id,
            'name' => $config['name'] ?? $id,
            'component' => $config['component'] ?? null,
            'view' => $config['view'] ?? null,
            'icon' => $config['icon'] ?? 'layout-dashboard',
            'description' => $config['description'] ?? '',
            'default_width' => $config['default_width'] ?? 4,
            'default_height' => $config['default_height'] ?? 2,
            'min_width' => $config['min_width'] ?? 2,
            'min_height' => $config['min_height'] ?? 1,
            'max_width' => $config['max_width'] ?? 12,
            'max_height' => $config['max_height'] ?? 6,
            'module' => $config['module'] ?? 'core/base',
            'props' => $config['props'] ?? [],
            'permissions' => $config['permissions'] ?? [],
        ], $config);

        return $this;
    }

    /**
     * Get all registered widgets
     */
    public function all(): array
    {
        return $this->widgets;
    }

    /**
     * Get widget by ID
     */
    public function get(string $id): ?array
    {
        return $this->widgets[$id] ?? null;
    }

    /**
     * Check if widget exists
     */
    public function has(string $id): bool
    {
        return isset($this->widgets[$id]);
    }

    /**
     * Get widgets filtered by permission
     */
    public function forUser($user = null): array
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return [];
        }

        return collect($this->widgets)
            ->filter(fn ($widget) => self::allows($user, $widget['permissions'] ?? []))
            ->all();
    }

    /**
     * Whether a user may see a widget that needs any of these permissions.
     */
    public static function allows($user, array $permissions): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->super_admin ?? false) {
            return true;
        }

        return empty($permissions) || $user->hasAnyPermission($permissions);
    }

    /**
     * Unregister a widget
     */
    public function unregister(string $id): self
    {
        unset($this->widgets[$id]);

        return $this;
    }
}
