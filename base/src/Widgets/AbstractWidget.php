<?php

namespace Polirium\Core\Base\Widgets;

use Livewire\Component;
use Polirium\Core\Base\Services\Dashboard\WidgetRegistry;

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
     * Runs on every request to the widget: the dashboard only mounts permitted
     * widgets, and this keeps a widget's data closed to anyone else.
     */
    public function boot(): void
    {
        abort_unless(WidgetRegistry::allows(auth()->user(), static::getPermissions()), 403);
    }

    /**
     * Shown while a lazily loaded widget fetches its data.
     */
    public function placeholder(): string
    {
        return <<<'HTML'
            <div class="card placeholder-glow" aria-busy="true">
                <div class="card-body">
                    <div class="placeholder col-5 mb-3"></div>
                    <div class="placeholder placeholder-lg col-12 mb-2"></div>
                    <div class="placeholder col-8 mb-2"></div>
                    <div class="placeholder col-6"></div>
                </div>
            </div>
            HTML;
    }

    /**
     * Period of a statistics widget: 'today', 'week' (last 7 days) or
     * 'month' (last 30 days), ending now.
     *
     * @return array{0: \Illuminate\Support\Carbon, 1: \Illuminate\Support\Carbon}
     */
    protected function periodRange(string $period): array
    {
        $end = now();

        return match ($period) {
            'week' => [today()->subDays(6), $end],
            'month' => [today()->subDays(29), $end],
            default => [today(), $end],
        };
    }

    /**
     * The same stretch of time just before (yesterday up to the same hour,
     * the 7 or 30 days before).
     *
     * @return array{0: \Illuminate\Support\Carbon, 1: \Illuminate\Support\Carbon}
     */
    protected function previousRange(string $period): array
    {
        [$start, $end] = $this->periodRange($period);
        $days = match ($period) {
            'week' => 7,
            'month' => 30,
            default => 1,
        };

        return [$start->copy()->subDays($days), $end->copy()->subDays($days)];
    }

    /**
     * Chart buckets of a period: hours of today, or the days of the range.
     *
     * @return list<string>
     */
    protected function periodBuckets(string $period): array
    {
        [$start, $end] = $this->periodRange($period);

        if ($period === 'today') {
            return array_map(fn ($hour) => (string) $hour, range(0, (int) $end->format('G')));
        }

        $buckets = [];
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $buckets[] = $day->toDateString();
        }

        return $buckets;
    }

    /**
     * Bucket of a moment for periodBuckets().
     */
    protected function bucketOf(string $period, \DateTimeInterface $moment): string
    {
        return $period === 'today' ? $moment->format('G') : $moment->format('Y-m-d');
    }

    /**
     * Percentage change against the previous period; null when there is
     * nothing to compare with.
     */
    protected function change(float $current, float $previous): ?float
    {
        if ($previous == 0.0) {
            return $current == 0.0 ? 0.0 : null;
        }

        return round(($current - $previous) / abs($previous) * 100, 1);
    }

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
