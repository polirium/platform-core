<?php

namespace Polirium\Core\Base\Services\Dashboard;

use Illuminate\Support\Facades\File;
use Polirium\Core\Base\Widgets\AbstractWidget;
use ReflectionClass;

/**
 * Widget Discovery Service
 *
 * Automatically discovers and registers widgets from all modules.
 * Widgets must extend AbstractWidget class.
 */
class WidgetDiscoveryService
{
    protected WidgetRegistry $registry;

    protected array $scanPaths = [];

    public function __construct(WidgetRegistry $registry)
    {
        $this->registry = $registry;
        $this->initScanPaths();
    }

    /**
     * Initialize scan paths for widget discovery
     */
    protected function initScanPaths(): void
    {
        $basePath = platform_path();

        // Core modules
        foreach (glob($basePath . 'core/*/src/Widgets') as $path) {
            if (is_dir($path)) {
                $this->scanPaths[] = $path;
            }
        }

        // Packages
        foreach (glob($basePath . 'packages/*/src/Widgets') as $path) {
            if (is_dir($path)) {
                $this->scanPaths[] = $path;
            }
        }

        // Modules
        foreach (glob($basePath . 'modules/*/src/Widgets') as $path) {
            if (is_dir($path)) {
                $this->scanPaths[] = $path;
            }
        }
    }

    /**
     * Discover and register all widgets
     */
    public function discover(): void
    {
        foreach ($this->scanPaths as $path) {
            $this->discoverInPath($path);
        }
    }

    /**
     * Discover widgets in a specific path
     */
    protected function discoverInPath(string $path): void
    {
        $files = File::glob($path . '/*.php');

        foreach ($files as $file) {
            $className = $this->getClassNameFromFile($file);

            if (! $className) {
                continue;
            }

            // Skip AbstractWidget itself
            if ($className === AbstractWidget::class) {
                continue;
            }

            // Check if class exists and extends AbstractWidget
            if (! class_exists($className)) {
                continue;
            }

            try {
                $reflection = new ReflectionClass($className);

                if ($reflection->isAbstract()) {
                    continue;
                }

                if (! $reflection->isSubclassOf(AbstractWidget::class)) {
                    continue;
                }

                // Register the widget
                $this->registerWidget($className);

            } catch (\Exception $e) {
                // Skip invalid classes
                continue;
            }
        }
    }

    /**
     * Extract class name from file
     */
    protected function getClassNameFromFile(string $file): ?string
    {
        $content = file_get_contents($file);

        // Extract namespace
        if (! preg_match('/namespace\s+([^;]+);/', $content, $nsMatch)) {
            return null;
        }

        $namespace = $nsMatch[1];

        // Extract class name
        if (! preg_match('/class\s+(\w+)/', $content, $classMatch)) {
            return null;
        }

        $className = $classMatch[1];

        return $namespace . '\\' . $className;
    }

    /**
     * Register a widget class
     */
    protected function registerWidget(string $className): void
    {
        /** @var AbstractWidget $className */
        $widgetId = $className::getWidgetId();
        $componentName = $this->generateComponentName($className);

        // Register as Livewire component
        \Livewire\Livewire::component($componentName, $className);

        // Register in dashboard widget registry
        $this->registry->register($widgetId, [
            'name' => $className::getWidgetName(),
            'component' => $componentName,
            'icon' => $className::getIcon(),
            'description' => $className::getDescription(),
            'default_width' => $className::getDefaultWidth(),
            'default_height' => $className::getDefaultHeight(),
            'permissions' => $className::getPermissions(),
            'class' => $className,
        ]);
    }

    /**
     * Generate Livewire component name from class
     */
    protected function generateComponentName(string $className): string
    {
        // Extract module namespace
        // Example: Polirium\Core\Base\Widgets\StatsWidget -> core/base::widgets.stats

        if (preg_match('/Polirium\\\\(Core|Packages|Modules)\\\\(\w+)\\\\Widgets\\\\(\w+)Widget$/i', $className, $matches)) {
            $type = strtolower($matches[1]); // core, packages, modules
            $module = $this->toKebabCase($matches[2]); // base, media, etc
            $widget = $this->toKebabCase(str_replace('Widget', '', $matches[3])); // stats, welcome, etc

            return "{$type}/{$module}::widgets.{$widget}";
        }

        // Fallback: use class alias
        return 'widget-' . $this->toKebabCase(class_basename($className));
    }

    /**
     * Convert string to kebab-case
     */
    protected function toKebabCase(string $string): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $string));
    }

    /**
     * Get all scan paths
     */
    public function getScanPaths(): array
    {
        return $this->scanPaths;
    }

    /**
     * Add custom scan path
     */
    public function addScanPath(string $path): self
    {
        if (is_dir($path) && ! in_array($path, $this->scanPaths)) {
            $this->scanPaths[] = $path;
        }

        return $this;
    }
}
