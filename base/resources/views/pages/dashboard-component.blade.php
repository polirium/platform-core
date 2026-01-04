<div>
    {{-- Dashboard Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="text-muted">
                        {{ trans('Chào mừng trở lại') }}, {{ auth()->user()->name ?? 'Admin' }}!
                    </div>
                </div>
                <div class="col-auto">
                    @if($editMode)
                        <button wire:click="cancelEdit" class="btn btn-ghost-secondary me-2">
                            {{ __('Hủy') }}
                        </button>
                        <button wire:click="saveLayout" class="btn btn-primary me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M6 4h10l4 4v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2v-12a2 2 0 0 1 2-2"/><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0-4 0"/><path d="M14 4l0 4l-6 0l0-4"/></svg>
                            {{ __('Lưu bố cục') }}
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#widgetPanel">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                            {{ __('Thêm Widget') }}
                        </button>
                    @else
                        <button wire:click="toggleEditMode" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37c1 .608 2.296.07 2.572-1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0"/></svg>
                            {{ __('Tùy chỉnh') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            {{-- Dynamic Widget Grid --}}
            <div class="row row-deck row-cards" id="dashboard-widgets">
                @forelse($layout as $item)
                    @php
                        $widget = collect($allWidgets)->firstWhere('id', $item['id']);
                        $colClass = 'col-md-' . ($item['w'] ?? 12);
                    @endphp

                    @if($widget)
                        <div class="{{ $colClass }} mb-3 widget-item" data-widget-id="{{ $item['id'] }}">
                            @if($editMode)
                                <div class="card h-100">
                                    <div class="card-header py-2 d-flex align-items-center">
                                        <span class="fw-bold">{{ $widget['name'] }}</span>
                                        <div class="ms-auto d-flex gap-1">
                                            {{-- Column size selector --}}
                                            <select wire:change="updateWidgetSize('{{ $item['id'] }}', $event.target.value)"
                                                    class="form-select form-select-sm" style="width: auto;">
                                                <option value="4" {{ ($item['w'] ?? 12) == 4 ? 'selected' : '' }}>1/3</option>
                                                <option value="6" {{ ($item['w'] ?? 12) == 6 ? 'selected' : '' }}>1/2</option>
                                                <option value="8" {{ ($item['w'] ?? 12) == 8 ? 'selected' : '' }}>2/3</option>
                                                <option value="12" {{ ($item['w'] ?? 12) == 12 ? 'selected' : '' }}>Full</option>
                                            </select>
                                            <button wire:click="moveWidgetUp('{{ $item['id'] }}')" class="btn btn-sm btn-ghost-secondary" title="Di chuyển lên">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M12 5l0 14"/><path d="M18 11l-6-6"/><path d="M6 11l6-6"/></svg>
                                            </button>
                                            <button wire:click="moveWidgetDown('{{ $item['id'] }}')" class="btn btn-sm btn-ghost-secondary" title="Di chuyển xuống">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M12 5l0 14"/><path d="M18 13l-6 6"/><path d="M6 13l6 6"/></svg>
                                            </button>
                                            <button wire:click="removeWidget('{{ $item['id'] }}')" class="btn btn-sm btn-ghost-danger" title="Xóa widget">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @livewire($widget['component'], [], key('w-' . $item['id']))
                                    </div>
                                </div>
                            @else
                                @livewire($widget['component'], [], key('w-' . $item['id']))
                            @endif
                        </div>
                    @endif
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <div class="mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" style="color: var(--tblr-muted);"><path d="M4 4h6v8h-6z"/><path d="M4 16h6v4h-6z"/><path d="M14 12h6v8h-6z"/><path d="M14 4h6v4h-6z"/></svg>
                                </div>
                                <h3 class="text-muted">{{ trans('Chưa có widget nào được bật') }}</h3>
                                <p class="text-muted mb-3">
                                    {{ trans('Nhấn nút Tùy chỉnh để thêm widget vào dashboard.') }}
                                </p>
                                <button wire:click="toggleEditMode" class="btn btn-primary">
                                    {{ __('Tùy chỉnh Dashboard') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Widget Panel Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="widgetPanel" wire:ignore.self>
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">{{ __('Thêm Widget') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <p class="text-muted mb-3">{{ __('Chọn widget để thêm vào dashboard:') }}</p>

            @foreach($unusedWidgets as $widget)
                <div class="card card-sm mb-2 cursor-pointer hover-shadow"
                     wire:click="addWidget('{{ $widget['id'] }}')"
                     data-bs-dismiss="offcanvas">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-primary-lt me-3">
                                @if($widget['icon'] === 'chart-bar')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M3 12m0 1a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1z"/><path d="M9 8m0 1a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1z"/><path d="M15 4m0 1a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1z"/></svg>
                                @elseif($widget['icon'] === 'home')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M5 12l-2 0l9-9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/><path d="M9 21v-6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M4 4h6v8h-6z"/><path d="M4 16h6v4h-6z"/><path d="M14 12h6v8h-6z"/><path d="M14 4h6v4h-6z"/></svg>
                                @endif
                            </span>
                            <div class="flex-fill">
                                <div class="fw-bold">{{ $widget['name'] }}</div>
                                <div class="text-muted small">{{ $widget['description'] ?? '' }}</div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        </div>
                    </div>
                </div>
            @endforeach

            @if(count($unusedWidgets) === 0)
                <div class="text-center text-muted py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M5 12l5 5l10-10"/></svg>
                    <p>{{ __('Tất cả widgets đã được thêm!') }}</p>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .cursor-pointer { cursor: pointer; }
        .hover-shadow:hover {
            box-shadow: var(--tblr-box-shadow);
            transform: translateY(-2px);
            transition: all 0.2s ease;
        }
        .widget-item.editing {
            border: 2px dashed var(--tblr-border-color);
            border-radius: var(--tblr-border-radius);
            padding: 0.5rem;
            background: rgba(var(--tblr-primary-rgb), 0.02);
        }
    </style>
    @endpush
</div>
