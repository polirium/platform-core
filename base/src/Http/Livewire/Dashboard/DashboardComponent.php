<?php

namespace Polirium\Core\Base\Http\Livewire\Dashboard;

use Livewire\Component;
use Polirium\Core\Base\Http\Models\UserDashboardLayout;

/**
 * Dashboard Component - Dynamic Widget-based Dashboard
 */
class DashboardComponent extends Component
{
    /**
     * All available widgets (filtered by permission)
     */
    public array $allWidgets = [];

    /**
     * Widget layout (id, x, y, w, h)
     */
    public array $layout = [];

    /**
     * Edit mode toggle
     */
    public bool $editMode = false;

    public function mount(): void
    {
        $this->loadWidgets();
        $this->loadUserLayout();
    }

    /**
     * Load available widgets from registry
     */
    protected function loadWidgets(): void
    {
        $registry = app('dashboard.widgets');
        $widgets = $registry->forUser(auth()->user());

        $this->allWidgets = collect($widgets)->values()->toArray();
    }

    /**
     * Load user's layout
     */
    protected function loadUserLayout(): void
    {
        $userId = auth()->id();
        $savedLayout = UserDashboardLayout::getLayoutForUser($userId);

        if (empty($savedLayout)) {
            // Default layout: all widgets with default sizes
            $this->layout = collect($this->allWidgets)->map(function ($widget, $index) {
                return [
                    'id' => $widget['id'],
                    'x' => 0,
                    'y' => $index,
                    'w' => $widget['default_width'] ?? 6,
                    'h' => $widget['default_height'] ?? 2,
                ];
            })->toArray();
        } else {
            // Map DB fields to component expected format
            $this->layout = collect($savedLayout)->map(function ($item) {
                return [
                    'id' => $item['widget_id'],
                    'x' => $item['position_x'] ?? 0,
                    'y' => $item['position_y'] ?? 0,
                    'w' => $item['width'] ?? 6,
                    'h' => $item['height'] ?? 2,
                ];
            })->values()->toArray();
        }
    }

    /**
     * Get enabled widget IDs
     */
    public function getEnabledWidgetsProperty(): array
    {
        return collect($this->layout)->pluck('id')->toArray();
    }

    /**
     * Get unused widgets
     */
    public function getUnusedWidgetsProperty(): array
    {
        $enabledIds = $this->enabledWidgets;

        return collect($this->allWidgets)
            ->filter(fn($widget) => !in_array($widget['id'], $enabledIds))
            ->values()
            ->toArray();
    }

    /**
     * Toggle edit mode
     */
    public function toggleEditMode(): void
    {
        $this->editMode = true;
        $this->dispatch('editModeChanged', editMode: true);
    }

    /**
     * Cancel edit
     */
    public function cancelEdit(): void
    {
        $this->editMode = false;
        $this->loadUserLayout();
        $this->dispatch('editModeChanged', editMode: false);
        $this->dispatch('layoutUpdated');
    }

    /**
     * Add widget to dashboard
     */
    public function addWidget(string $widgetId): void
    {
        $widget = collect($this->allWidgets)->firstWhere('id', $widgetId);

        if (!$widget) {
            return;
        }

        // Find the next available Y position
        $maxY = collect($this->layout)->max('y') ?? -1;

        $this->layout[] = [
            'id' => $widgetId,
            'x' => 0,
            'y' => $maxY + 1,
            'w' => $widget['default_width'] ?? 6,
            'h' => $widget['default_height'] ?? 2,
        ];

        // Dispatch event to refresh grid
        $this->dispatch('layoutUpdated');
    }

    /**
     * Remove widget from dashboard
     */
    public function removeWidget(string $widgetId): void
    {
        $this->layout = array_values(
            array_filter($this->layout, fn($item) => $item['id'] !== $widgetId)
        );

        // Dispatch event to refresh grid
        $this->dispatch('layoutUpdated');
    }

    /**
     * Update widget column size
     */
    public function updateWidgetSize(string $widgetId, int $width): void
    {
        foreach ($this->layout as &$item) {
            if ($item['id'] === $widgetId) {
                $item['w'] = (int) $width;
                break;
            }
        }
    }

    /**
     * Move widget up in order
     */
    public function moveWidgetUp(string $widgetId): void
    {
        $index = $this->findWidgetIndex($widgetId);

        if ($index > 0) {
            $temp = $this->layout[$index - 1];
            $this->layout[$index - 1] = $this->layout[$index];
            $this->layout[$index] = $temp;
        }
    }

    /**
     * Move widget down in order
     */
    public function moveWidgetDown(string $widgetId): void
    {
        $index = $this->findWidgetIndex($widgetId);

        if ($index < count($this->layout) - 1) {
            $temp = $this->layout[$index + 1];
            $this->layout[$index + 1] = $this->layout[$index];
            $this->layout[$index] = $temp;
        }
    }

    /**
     * Find widget index in layout
     */
    protected function findWidgetIndex(string $widgetId): int
    {
        foreach ($this->layout as $index => $item) {
            if ($item['id'] === $widgetId) {
                return $index;
            }
        }
        return -1;
    }

    /**
     * Update order from drag-drop (wire:sortable)
     */
    public function updateOrder(array $items): void
    {
        $orderedLayout = [];

        foreach ($items as $item) {
            $widgetId = $item['value'];
            $existingItem = collect($this->layout)->firstWhere('id', $widgetId);

            if ($existingItem) {
                $orderedLayout[] = $existingItem;
            }
        }

        $this->layout = $orderedLayout;
    }

    /**
     * Save layout
     */
    public function saveLayout(): void
    {
        UserDashboardLayout::saveLayoutForUser(auth()->id(), $this->layout);
        $this->editMode = false;

        $this->dispatch('editModeChanged', editMode: false);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Đã lưu bố cục dashboard'),
        ]);
    }

    public function render()
    {
        return view('core/base::pages.dashboard-component', [
            'unusedWidgets' => $this->unusedWidgets,
        ]);
    }
}
