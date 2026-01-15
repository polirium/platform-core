<!--
    Single Action Button Component
    Internal component for action-buttons
-->

@props([
    'action' => [],
    'row' => null,
    'sizeClass' => '',
    'isDropdownTrigger' => false,
])

@php
    $type = $action['type'] ?? 'edit';
    $icon = $action['icon'] ?? match($type) {
        'edit' => 'pencil',
        'delete' => 'trash',
        'view' => 'eye',
        'copy' => 'copy',
        'active' => 'check',
        'inactive' => 'x',
        'more' => 'dots',
        default => 'circle'
    };

    $label = $action['label'] ?? match($type) {
        'edit' => __('core/base::general.edit'),
        'delete' => __('core/base::general.delete'),
        'view' => __('core/base::general.view'),
        'copy' => __('core/base::general.copy'),
        'active' => __('core/base::general.active'),
        'inactive' => __('core/base::general.inactive'),
        'more' => __('core/base::general.more'),
        default => 'Action'
    };

    $tooltip = $action['tooltip'] ?? $label;
    $actionAttr = $action['action'] ?? '';
    $confirm = $action['confirm'] ?? null;
@endphp

@if($isDropdownTrigger)
    {{-- Dropdown trigger - icon only với dropdown icon --}}
    <button
        class="action-btn {{ $type }} icon-only {{ $sizeClass }}"
        data-tooltip="{{ $tooltip }}"
        {{ $actionAttr }}
        @if($confirm) wire:confirm="{{ $confirm }}" @endif
        aria-label="{{ $label }}"
    >
        {!! tabler_icon($icon, ['class' => 'icon']) !!}
    </button>
@elseif($type === 'divider')
    {{-- Divider cho dropdown --}}
    <div class="action-dropdown-divider"></div>
@else
    {{-- Regular action button --}}
    <button
        class="action-btn {{ $type }} {{ $sizeClass }}"
        data-tooltip="{{ $tooltip }}"
        {{ $actionAttr }}
        @if($confirm) wire:confirm="{{ $confirm }}" @endif
        aria-label="{{ $label }}"
    >
        <span class="icon">{!! tabler_icon($icon, ['class' => 'icon']) !!}</span>
        <span class="action-text">{{ $label }}</span>
    </button>
@endif
