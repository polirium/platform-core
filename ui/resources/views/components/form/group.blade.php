@props([
    'label' => null,
    'description' => null,
    'icon' => null,
    'collapsible' => false,
    'collapsed' => false,
    'borderless' => false,
    'compact' => false,
])

@php
    $groupId = 'group-' . uniqid();
    $marginClass = $compact ? 'mb-2' : 'mb-4';
@endphp

<div class="{{ $borderless ? 'ui-form-fieldset-borderless' : 'ui-form-fieldset' }} {{ $marginClass }}" {{ $attributes->only(['x-data', 'x-init']) }}>
    {{-- Fieldset Header --}}
    @if ($label || $icon)
        <div class="ui-form-fieldset-header d-flex align-items-center justify-content-between {{ $collapsible ? 'cursor-pointer' : '' }} mb-3"
             @if ($collapsible)
                x-data="{ open: {{ $collapsed ? 'false' : 'true' }} }"
                @click="open = !open"
             @endif
        >
            <div class="d-flex align-items-center gap-2">
                @if ($icon)
                    <div class="ui-fieldset-icon">
                        {!! tabler_icon($icon, ['class' => 'icon text-primary']) !!}
                    </div>
                @endif

                @if ($label)
                    <h3 class="ui-form-fieldset-title mb-0">{{ $label }}</h3>
                @endif
            </div>

            @if ($collapsible)
                <i class="ti ti-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
            @endif
        </div>
    @endif

    {{-- Fieldset Description --}}
    @if ($description)
        <p class="ui-form-fieldset-description text-muted small mb-3">{{ $description }}</p>
    @endif

    {{-- Fieldset Body --}}
    <div class="ui-form-fieldset-body"
         @if ($collapsible)
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
         @endif
    >
        <div class="row g-3">
            {{ $slot }}
        </div>
    </div>
</div>

@once
@push('styles')
<style>
    .ui-form-fieldset {
        background-color: var(--tblr-bg-surface);
        border: 1px solid var(--tblr-border-color);
        border-radius: 0.5rem;
        padding: 1.25rem;
        transition: box-shadow 0.2s ease;
    }

    .ui-form-fieldset:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .ui-form-fieldset-header {
        user-select: none;
    }

    .ui-form-fieldset-header.collapsible:hover {
        background-color: var(--tblr-bg-muted);
        margin: -1.25rem -1.25rem 1rem -1.25rem;
        padding: 1rem 1.25rem;
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .ui-fieldset-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        background-color: var(--tblr-primary-bg-subtle);
        border-radius: 0.375rem;
    }

    .ui-form-fieldset-title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--tblr-body-color);
        letter-spacing: 0.01em;
    }

    .ui-form-fieldset-description {
        line-height: 1.5;
    }

    .ui-form-fieldset-body {
        position: relative;
    }

    /* Collapsible transition */
    .ui-form-fieldset-header .ti-chevron-down {
        transition: transform 0.2s ease;
    }

    /* Nested fieldsets */
    .ui-form-fieldset .ui-form-fieldset {
        background-color: transparent;
        border-color: var(--tblr-border-color-translucent);
        padding: 1rem;
    }

    /* Borderless variant */
    .ui-form-fieldset-borderless {
        background-color: transparent;
        border: none;
        padding: 0;
    }

    .ui-form-fieldset-borderless:hover {
        box-shadow: none;
    }

    .ui-form-fieldset-borderless .ui-form-fieldset-header {
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--tblr-border-color);
        margin-bottom: 1rem;
    }
</style>
@endpush
@endonce
