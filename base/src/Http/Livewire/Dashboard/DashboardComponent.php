<?php

namespace Polirium\Core\Base\Http\Livewire\Dashboard;

use Livewire\Component;

/**
 * Dashboard Component - Widget-based Dashboard System
 *
 * This component will be enhanced to support:
 * - Drag & drop widget arrangement
 * - Dynamic widget registration from modules
 * - Per-user dashboard layout persistence
 *
 * @todo Implement widget registry system
 * @todo Add drag & drop functionality
 * @todo Store user dashboard preferences
 */
class DashboardComponent extends Component
{
    /**
     * Registered widgets for the dashboard
     */
    public array $widgets = [];

    public function mount(): void
    {
        // Load registered widgets
        // $this->widgets = app('dashboard.widgets')->all();
    }

    public function render()
    {
        return view('core/base::pages.dashboard-component', [
            'widgets' => $this->widgets,
        ]);
    }
}
