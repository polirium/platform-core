<!--
    Polirium Action Buttons Component
    Table row actions với tooltip và responsive

    Usage:
        <x-ui::action-buttons :actions="$actions" :row="$row />

    Actions format:
        [
            [
                'type' => 'edit|delete|view|copy|active|inactive|more',
                'icon' => 'tabler-icon-name',
                'label' => 'Button Label',
                'action' => 'wire:click or href',
                'confirm' => 'Confirm message (optional)',
                'tooltip' => 'Tooltip text (optional, defaults to label)',
            ],
            ...
        ]
-->

@props([
    'actions' => [],
    'row' => null,
    'compact' => false,        // Force icon-only mode
    'expanded' => false,       // Force icon+text mode
    'stackMobile' => false,    // Stack buttons on mobile
    'size' => 'md',           // sm|md|lg
])

@php
    $sizeClass = match($size) {
        'sm' => 'sm',
        'lg' => 'lg',
        default => ''
    };

    $containerClasses = collect([
        'action-buttons',
        $compact ? 'compact' : null,
        $expanded ? 'expanded' : null,
        $stackMobile ? 'stack-mobile' : null,
    ])->filter()->implode(' ');

    // Xác định xem có nên group vào dropdown không
    $needsDropdown = count($actions) > 3;
@endphp

<div class="{{ $containerClasses }}">
    @if($needsDropdown)
        {{-- DROPDOWN GROUP - Cho nhiều actions --}}
        <div class="action-dropdown" x-data="{ open: false }">
            {{-- Primary action (Edit/View) - Luôn hiện --}}
            @php
                $primaryAction = collect($actions)->first(fn($a) => in_array($a['type'] ?? '', ['edit', 'view']));
                $dropdownActions = collect($actions)->filter(fn($a) => $a !== $primaryAction);
            @endphp

            @if($primaryAction)
                @include('core.ui.components.action-button', [
                    'action' => $primaryAction,
                    'row' => $row,
                    'sizeClass' => $sizeClass,
                    'isDropdownTrigger' => false
                ])
            @endif

            {{-- More Dropdown Button --}}
            <button
                class="action-btn more icon-only {{ $sizeClass }}"
                data-tooltip="Thêm"
                @click="open = !open"
                aria-label="More actions"
                aria-expanded="{{ $open ? 'true' : 'false' }}"
            >
                {!! tabler_icon('dots', ['class' => 'icon']) !!}
            </button>

            {{-- Dropdown Menu --}}
            <div class="action-dropdown-menu" :class="{ show: open }" @click.away="open = false" x-show="open" x-cloak>
                @foreach($dropdownActions as $action)
                    @if(($action['divider'] ?? false))
                        <div class="action-dropdown-divider"></div>
                    @else
                        <button
                            class="action-dropdown-item"
                            @if(isset($action['action'])) {!! $action['action'] !!} @endif
                            @if(isset($action['confirm'])) wire:confirm="{{ $action['confirm'] }}" @endif
                        >
                            @if(isset($action['icon']))
                                <span class="icon">{!! tabler_icon($action['icon']) !!}</span>
                            @endif
                            <span>{{ $action['label'] }}</span>
                            @if(isset($action['shortcut']))
                                <span class="shortcut">{{ $action['shortcut'] }}</span>
                            @endif
                        </button>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        {{-- INDIVIDUAL BUTTONS - Cho ít actions (<=3) --}}
        @foreach($actions as $action)
            @include('core.ui.components.action-button', [
                'action' => $action,
                'row' => $row,
                'sizeClass' => $sizeClass
            ])
        @endforeach
    @endif
</div>

<style>
    /* Load action-buttons CSS nếu chưa load */
    @if(!app('assets')->isLoaded('action-buttons'))
        @php app('assets')->markLoaded('action-buttons') @endphp
    @endif
</style>
