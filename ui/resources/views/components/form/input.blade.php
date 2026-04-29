@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'append' => null,
    'prepend' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'icon' => null,
    'horizontal' => false,
    'labelWidth' => 'col-sm-4',
    'inputWidth' => 'col-sm-8',
    'compact' => false,
])

@php
    $hasError = $errors->has($name);
    $inputId = $attributes->get('id') ?? 'input-' . str_replace('_', '-', $name ?? uniqid());
    $marginClass = $compact ? 'mb-2' : 'mb-3';
@endphp

@if ($horizontal)
{{-- Horizontal Layout --}}
<div class="ui-form-group row align-items-center {{ $marginClass }}" {{ $attributes->only(['x-data', 'x-init']) }}>
    @if ($label)
        <label for="{{ $inputId }}" class="{{ $labelWidth }} col-form-label ui-form-label py-0">
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    <div class="{{ $label ? $inputWidth : 'col-12' }}">
        @if ($prepend || $append || $icon)
            <div class="ui-input-wrapper position-relative">
                @if ($prepend)
                    <div class="ui-input-prepend">{{ $prepend }}</div>
                @endif
                @if ($icon)
                    <div class="ui-input-icon position-absolute top-50 start-0 translate-middle-y ps-3">
                        {!! tabler_icon($icon, ['class' => 'icon text-muted']) !!}
                    </div>
                @endif
                <input
                    id="{{ $inputId }}"
                    {{ $attributes->class([
                        'ui-form-control',
                        'form-control',
                        'form-control-sm' => $compact,
                        'is-invalid' => $hasError,
                        'with-icon' => $icon,
                        'with-prepend' => $prepend,
                        'with-append' => $append,
                    ])->merge([
                        'name' => $name,
                        'disabled' => $disabled,
                        'readonly' => $readonly,
                    ]) }}
                />
                @if ($append)
                    <div class="ui-input-append">{{ $append }}</div>
                @endif
            </div>
        @else
            <input
                id="{{ $inputId }}"
                {{ $attributes->class([
                    'ui-form-control',
                    'form-control',
                    'form-control-sm' => $compact,
                    'is-invalid' => $hasError,
                ])->merge([
                    'name' => $name,
                    'disabled' => $disabled,
                    'readonly' => $readonly,
                ]) }}
            />
        @endif
        @if ($hint)
            <p class="ui-form-hint text-muted small mt-1 mb-0">{{ $hint }}</p>
        @endif
        @if ($hasError)
            <div class="ui-form-error text-danger small mt-1">{{ $errors->first($name) }}</div>
        @endif
    </div>
</div>
@else
{{-- Vertical Layout (Default) --}}
<div class="ui-form-group {{ $marginClass }}" {{ $attributes->only(['x-data', 'x-init']) }}>
    @if ($label)
        <label for="{{ $inputId }}" class="ui-form-label d-block mb-2">
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if ($description)
        <p class="ui-form-description text-muted small mb-2">{{ $description }}</p>
    @endif

    @if ($prepend || $append || $icon)
        <div class="ui-input-wrapper position-relative">
            @if ($prepend)
                <div class="ui-input-prepend">{{ $prepend }}</div>
            @endif
            @if ($icon)
                <div class="ui-input-icon position-absolute top-50 start-0 translate-middle-y ps-3">
                    {!! tabler_icon($icon, ['class' => 'icon text-muted']) !!}
                </div>
            @endif
            <input
                id="{{ $inputId }}"
                {{ $attributes->class([
                    'ui-form-control',
                    'form-control',
                    'form-control-sm' => $compact,
                    'is-invalid' => $hasError,
                    'with-icon' => $icon,
                    'with-prepend' => $prepend,
                    'with-append' => $append,
                ])->merge([
                    'name' => $name,
                    'disabled' => $disabled,
                    'readonly' => $readonly,
                ]) }}
            />
            @if ($append)
                <div class="ui-input-append">{{ $append }}</div>
            @endif
        </div>
    @else
        <input
            id="{{ $inputId }}"
            {{ $attributes->class([
                'ui-form-control',
                'form-control',
                'form-control-sm' => $compact,
                'is-invalid' => $hasError,
            ])->merge([
                'name' => $name,
                'disabled' => $disabled,
                'readonly' => $readonly,
            ]) }}
        />
    @endif

    @if ($hint)
        <p class="ui-form-hint text-muted small mt-1">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <div class="ui-form-error text-danger small mt-1">
            <i class="ti ti-circle-x me-1"></i>
            {{ $errors->first($name) }}
        </div>
    @endif
</div>
@endif

@once
@push('styles')
<style>
    /* Modern Form Styles - Minimalism & Swiss Style */
    .ui-form-control {
        min-height: 42px;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border: 1px solid var(--tblr-border-color);
        border-radius: 0.375rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        background-color: var(--tblr-bg-surface);
    }

    .ui-form-control:hover:not(:disabled):not([readonly]) {
        border-color: var(--tblr-border-color-translucent);
    }

    .ui-form-control:focus {
        border-color: var(--tblr-primary);
        box-shadow: 0 0 0 3px rgba(var(--tblr-primary-rgb), 0.1);
        background-color: var(--tblr-bg-surface);
    }

    .ui-form-control.is-invalid {
        border-color: var(--tblr-danger);
    }

    .ui-form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(var(--tblr-danger-rgb), 0.1);
    }

    .ui-form-control.with-icon {
        padding-left: 2.75rem;
    }

    .ui-form-control.with-prepend {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .ui-form-control.with-append {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .ui-input-wrapper .ui-input-prepend {
        display: flex;
        align-items: center;
        padding: 0 0.75rem;
        background-color: var(--tblr-bg-muted);
        border: 1px solid var(--tblr-border-color);
        border-right: none;
        border-radius: 0.375rem 0 0 0.375rem;
        color: var(--tblr-muted);
        font-size: 0.875rem;
    }

    .ui-input-wrapper .ui-input-append {
        display: flex;
        align-items: center;
        padding: 0 0.75rem;
        background-color: var(--tblr-bg-muted);
        border: 1px solid var(--tblr-border-color);
        border-left: none;
        border-radius: 0 0.375rem 0.375rem 0;
        color: var(--tblr-muted);
        font-size: 0.875rem;
    }

    .ui-input-wrapper .ui-input-icon {
        pointer-events: none;
        z-index: 1;
    }

    .ui-form-label {
        font-weight: 500;
        font-size: 0.8125rem;
        color: var(--tblr-body-color);
        letter-spacing: 0.01em;
    }

    .ui-form-description {
        line-height: 1.4;
    }

    .ui-form-hint {
        line-height: 1.4;
    }

    .ui-form-error {
        display: flex;
        align-items: center;
        line-height: 1.4;
    }

    /* Dark mode improvements */
    [data-bs-theme="dark"] .ui-form-control {
        background-color: var(--tblr-bg-surface);
        border-color: var(--tblr-border-color);
    }

    [data-bs-theme="dark"] .ui-form-control:focus {
        background-color: var(--tblr-bg-surface);
        border-color: var(--tblr-primary);
    }

    /* Disabled state */
    .ui-form-control:disabled {
        background-color: var(--tblr-bg-muted);
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Readonly state */
    .ui-form-control[readonly] {
        background-color: var(--tblr-bg-muted);
        cursor: default;
    }
</style>
@endpush
@endonce
