<?php

namespace Polirium\Core\Base\Http\Livewire\Dashboard;

use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\UserDashboardLayout;
use Throwable;

/**
 * Dashboard Component - widgets on a 12-column grid (gridstack.js).
 *
 * Positions and widths are chosen by the user; heights follow each widget's
 * content, so the stored height is informative only.
 */
class DashboardComponent extends Component
{
    private const COLUMNS = 12;

    private const MIN_WIDTH = 3;

    /**
     * All available widgets (filtered by permission). Locked: the browser must
     * not be able to add widgets or components the user may not see.
     */
    #[Locked]
    public array $allWidgets = [];

    /**
     * Widget layout (id, x, y, w, h). Locked: changed only by the actions below.
     */
    #[Locked]
    public array $layout = [];

    /**
     * Edit mode toggle
     */
    public bool $editMode = false;

    public function mount(): void
    {
        \Polirium\Core\UI\Facades\Assets::loadCss(['gridstack'])->loadJs(['gridstack', 'dashboard']);
        $this->loadWidgets();
        $this->loadUserLayout();
    }

    /**
     * Load available widgets from registry
     */
    protected function loadWidgets(): void
    {
        $this->allWidgets = collect(app('dashboard.widgets')->forUser(auth()->user()))->values()->toArray();
    }

    /**
     * Load the user's layout, or the default one if they never customised it.
     */
    protected function loadUserLayout(): void
    {
        $userId = auth()->id();
        $permittedIds = collect($this->allWidgets)->pluck('id')->all();

        if (! UserDashboardLayout::hasLayout($userId)) {
            $this->layout = $this->defaultLayout();

            return;
        }

        // Rows for widgets the user may not see now (permission revoked,
        // module disabled) are kept, so the layout comes back unchanged if
        // access is restored.
        $layout = collect(UserDashboardLayout::getLayoutForUser($userId))
            ->filter(fn ($item) => in_array($item['widget_id'], $permittedIds, true))
            ->map(fn ($item) => [
                'id' => $item['widget_id'],
                'x' => (int) ($item['position_x'] ?? 0),
                'y' => (int) ($item['position_y'] ?? 0),
                'w' => (int) ($item['width'] ?? 6),
                'h' => (int) ($item['height'] ?? 2),
            ])
            ->values()
            ->all();

        // Layouts saved before the grid (1.0.x) kept only an order: every
        // widget sits at x = 0. Lay them out side by side in that order.
        if (count($layout) > 1 && collect($layout)->every(fn ($item) => $item['x'] === 0)) {
            $layout = $this->pack($layout);
        }

        // Widgets that are new to this user (registered after they customised,
        // or just permitted) are appended once; removed ones stay removed.
        foreach ($this->allWidgets as $widget) {
            if (collect($layout)->contains('id', $widget['id']) || UserDashboardLayout::hasWidgetRecord($userId, $widget['id'])) {
                continue;
            }

            $item = ['id' => $widget['id'], 'x' => 0, 'y' => $this->bottom($layout), 'w' => $this->width($widget['default_width'] ?? 6), 'h' => 2];
            $layout[] = $item;
            UserDashboardLayout::addWidgetForUser($userId, $widget['id'], $item);
        }

        $this->layout = $layout;
    }

    /**
     * Every permitted widget at its default width, filling rows left to right.
     */
    protected function defaultLayout(): array
    {
        return $this->pack(collect($this->allWidgets)->map(fn ($widget) => [
            'id' => $widget['id'],
            'x' => 0,
            'y' => 0,
            'w' => $this->width($widget['default_width'] ?? 6),
            'h' => 2,
        ])->all());
    }

    /**
     * Place items in order, left to right, wrapping when a row is full.
     */
    protected function pack(array $items): array
    {
        $x = 0;
        $y = 0;

        return collect($items)->map(function ($item) use (&$x, &$y) {
            $w = $this->width($item['w']);

            if ($x + $w > self::COLUMNS) {
                $x = 0;
                $y++;
            }

            $placed = [...$item, 'x' => $x, 'y' => $y, 'w' => $w];
            $x += $w;

            return $placed;
        })->all();
    }

    protected function width(mixed $width): int
    {
        return max(self::MIN_WIDTH, min(self::COLUMNS, (int) $width ?: 6));
    }

    protected function bottom(array $layout): int
    {
        return (int) collect($layout)->map(fn ($item) => $item['y'] + max(1, $item['h']))->max();
    }

    protected function refreshGrid(): void
    {
        $this->dispatch('dashboard-grid-refresh');
    }

    /**
     * Get unused widgets
     */
    public function getUnusedWidgetsProperty(): array
    {
        $enabledIds = collect($this->layout)->pluck('id')->all();

        return collect($this->allWidgets)
            ->reject(fn ($widget) => in_array($widget['id'], $enabledIds, true))
            ->values()
            ->toArray();
    }

    public function toggleEditMode(): void
    {
        $this->editMode = true;
        $this->refreshGrid();
    }

    public function cancelEdit(): void
    {
        $this->editMode = false;
        $this->loadUserLayout();
        $this->refreshGrid();
    }

    /**
     * Add widget to the bottom of the dashboard
     */
    public function addWidget(string $widgetId): void
    {
        $widget = collect($this->allWidgets)->firstWhere('id', $widgetId);

        if (! $widget || collect($this->layout)->contains('id', $widgetId)) {
            return;
        }

        $this->layout[] = [
            'id' => $widgetId,
            'x' => 0,
            'y' => $this->bottom($this->layout),
            'w' => $this->width($widget['default_width'] ?? 6),
            'h' => 2,
        ];

        $this->refreshGrid();
    }

    public function removeWidget(string $widgetId): void
    {
        $this->layout = collect($this->layout)->reject(fn ($item) => $item['id'] === $widgetId)->values()->all();
        $this->refreshGrid();
    }

    /**
     * Positions reported by the grid after a drag or resize. The grid already
     * shows them, so nothing is re-rendered.
     *
     * @param array<int, array{id?: string, x?: int, y?: int, w?: int, h?: int}> $items
     */
    #[Renderless]
    public function updateGrid(array $items): void
    {
        $positions = collect($items)->keyBy(fn ($item) => (string) ($item['id'] ?? ''));

        $this->layout = collect($this->layout)->map(function ($item) use ($positions) {
            $position = $positions->get($item['id']);

            if (! $position) {
                return $item;
            }

            $w = $this->width($position['w'] ?? $item['w']);

            return [
                'id' => $item['id'],
                'x' => max(0, min(self::COLUMNS - $w, (int) ($position['x'] ?? $item['x']))),
                'y' => max(0, (int) ($position['y'] ?? $item['y'])),
                'w' => $w,
                'h' => max(1, (int) ($position['h'] ?? $item['h'])),
            ];
        })->sortBy([['y', 'asc'], ['x', 'asc']])->values()->all();
    }

    public function saveLayout(): void
    {
        $permitted = collect($this->allWidgets)->pluck('id')->all();
        $this->layout = collect($this->layout)
            ->filter(fn ($item) => in_array($item['id'], $permitted, true))
            ->values()
            ->all();

        try {
            UserDashboardLayout::saveLayoutForUser(
                auth()->id(),
                $this->layout,
                array_values(array_diff($permitted, collect($this->layout)->pluck('id')->all())),
            );
        } catch (Throwable $exception) {
            report($exception);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => trans('core/base::general.dashboard_layout_save_failed'),
            ]);

            return;
        }

        $this->editMode = false;
        $this->refreshGrid();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => trans('core/base::general.dashboard_layout_saved'),
        ]);
    }

    /**
     * Forget the user's layout and show the default one.
     */
    public function resetLayout(): void
    {
        UserDashboardLayout::where('user_id', auth()->id())->delete();

        $this->editMode = false;
        $this->layout = $this->defaultLayout();
        $this->refreshGrid();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => trans('core/base::general.dashboard_layout_reset'),
        ]);
    }

    public function render()
    {
        return view('core/base::pages.dashboard-component', [
            'unusedWidgets' => $this->unusedWidgets,
            'widgets' => collect($this->allWidgets)->keyBy('id'),
        ]);
    }
}
