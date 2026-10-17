<div class="dashboard-container" data-edit-mode="{{ $editMode ? 'true' : 'false' }}">
    {{-- Dashboard Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle text-muted">
                        {{ __('core/base::general.welcome_back') }}
                    </div>
                    <h2 class="page-title">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </h2>
                </div>
                <div class="col-auto d-flex gap-2">
                    @if($editMode)
                        <x-ui::button type="button" color="danger" outline icon="restore" wire:click="resetLayout"
                                wire:confirm="{{ __('core/base::general.reset_layout_confirm') }}">
                            {{ __('core/base::general.reset_layout') }}
                        </x-ui::button>
                        <x-ui::button type="button" color="secondary" ghost icon="x" wire:click="cancelEdit">
                            {{ __('core/base::general.cancel') }}
                        </x-ui::button>
                        <x-ui::button type="button" color="secondary" outline icon="plus" data-bs-toggle="offcanvas" data-bs-target="#widgetPanel">
                            {{ __('core/base::general.add_widget') }}
                        </x-ui::button>
                        <x-ui::button type="button" color="primary" icon="device-floppy" wire:click="saveLayout">
                            {{ __('core/base::general.save_layout') }}
                        </x-ui::button>
                    @else
                        <x-ui::button type="button" color="secondary" outline icon="settings" wire:click="toggleEditMode">
                            {{ __('core/base::general.customize') }}
                        </x-ui::button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            {{-- Edit Mode Banner --}}
            <div class="alert alert-info mb-4 {{ $editMode ? '' : 'd-none' }}" role="alert">
                <div class="d-flex align-items-center">
                    <x-tabler-icons::info-circle class="icon alert-icon me-2" />
                    <div>
                        <strong>{{ __('core/base::general.customize') }}</strong>:
                        {{ __('core/base::general.edit_mode_hint') }}
                    </div>
                </div>
            </div>

            {{-- Widget Grid (gridstack.js): same markup in view and edit mode, so
                 widgets keep their state when the editor opens. --}}
            @if(count($layout) > 0)
                <div class="grid-stack dashboard-grid" id="dashboard-grid">
                    @foreach($layout as $item)
                        @php $widget = $widgets->get($item['id']); @endphp

                        @if($widget)
                            <div class="grid-stack-item" wire:key="widget-item-{{ $item['id'] }}"
                                 gs-id="{{ $item['id'] }}" gs-x="{{ $item['x'] }}" gs-y="{{ $item['y'] }}"
                                 gs-w="{{ $item['w'] }}" gs-h="{{ $item['h'] }}" gs-min-w="3">
                                <div class="grid-stack-item-content">
                                    <div class="dashboard-widget">
                                        <div class="widget-edit-toolbar">
                                            <div class="d-flex align-items-center min-w-0">
                                                <span class="widget-drag-handle me-2" title="{{ __('core/base::general.customize') }}">
                                                    <x-tabler-icons::grip-vertical class="icon" />
                                                </span>
                                                <span class="widget-edit-title text-truncate">{{ $widget['name'] }}</span>
                                            </div>
                                            <button type="button" class="btn btn-sm widget-remove-btn"
                                                    wire:click="removeWidget('{{ $item['id'] }}')" aria-label="{{ __('core/base::general.delete') }}">
                                                <x-tabler-icons::x class="icon" />
                                            </button>
                                        </div>
                                        <div class="widget-body">
                                            @livewire($widget['component'], ['lazy' => true], key('widget-' . $item['id']))
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-3 text-muted">
                            <x-tabler-icons::layout-dashboard class="icon" width="64" height="64" stroke-width="1.5" />
                        </div>
                        <h3 class="text-muted">{{ __('core/base::general.no_widgets_yet') }}</h3>
                        <p class="text-muted mb-4">
                            {{ __('core/base::general.no_widgets_hint') }}
                        </p>
                        <button wire:click="toggleEditMode" class="btn btn-primary btn-lg">
                            <x-tabler-icons::plus class="icon me-1" />
                            {{ __('core/base::general.customize_dashboard') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Widget Panel Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="widgetPanel" wire:ignore.self style="width: 380px;">
        <div class="offcanvas-header border-bottom">
            <div>
                <h5 class="offcanvas-title mb-1">{{ __('core/base::general.add_widget') }}</h5>
                <p class="text-muted small mb-0">{{ __('core/base::general.select_widget_to_add') }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            @if(count($unusedWidgets) > 0)
                <div class="list-group list-group-flush">
                    @foreach($unusedWidgets as $widget)
                        <a href="#" class="list-group-item list-group-item-action"
                           wire:click.prevent="addWidget('{{ $widget['id'] }}')"
                           data-bs-dismiss="offcanvas">
                            <div class="d-flex align-items-center">
                                <span class="avatar bg-primary-lt me-3">
                                    @php $iconName = 'tabler-icons::' . ($widget['icon'] ?? 'layout-grid'); @endphp
                                    <x-dynamic-component :component="$iconName" class="icon text-primary" />
                                </span>
                                <div class="flex-fill">
                                    <div class="fw-bold">{{ $widget['name'] }}</div>
                                    <div class="text-muted small">{{ $widget['description'] ?? '' }}</div>
                                </div>
                                <x-tabler-icons::plus class="icon text-primary" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <x-tabler-icons::checkbox class="icon mb-3" width="48" height="48" />
                    <p class="mb-0">{{ __('core/base::general.all_widgets_added') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
