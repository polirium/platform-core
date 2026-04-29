@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'switch' => false,
    'inline' => false,
])

@php
    $hasError = $errors->has($name);
    $inputId = $attributes->get('id') ?? 'checkbox-' . str_replace('_', '-', $name ?? uniqid());
    $wrapperClass = $switch ? 'form-switch' : 'form-check';
    $inputClass = $switch ? 'form-check-input' : 'form-check-input';
@endphp

<div class="ui-form-group {{ $inline ? 'ui-form-inline' : '' }} mb-4" {{ $attributes->only(['x-data', 'x-init']) }}>
    <label class="{{ $wrapperClass }} ui-form-checkbox {{ $disabled ? 'is-disabled' : '' }}" for="{{ $inputId }}">
        <input
            id="{{ $inputId }}"
            type="checkbox"
            {{ $attributes->class([$inputClass, 'is-invalid' => $hasError])->merge([
                'disabled' => $disabled,
            ]) }}
        />

        <span class="ui-checkbox-label">
            @if ($label)
                <span class="ui-checkbox-text">{{ $label }}</span>
            @endif

            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </span>

        @if ($description)
            <span class="ui-checkbox-description text-muted small d-block">{{ $description }}</span>
        @endif
    </label>

    {{-- Hint --}}
    @if ($hint && !$inline)
        <p class="ui-form-hint text-muted small mt-1">{{ $hint }}</p>
    @endif

    {{-- Error --}}
    @if ($hasError && !$inline)
        <div class="ui-form-error text-danger small mt-1">
            <i class="ti ti-circle-x me-1"></i>
            {{ $errors->first($name) }}
        </div>
    @endif
</div>

@once
@push('styles')
<style>
    .ui-form-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.25rem 0;
        user-select: none;
    }

    .ui-form-checkbox.is-disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .ui-form-checkbox .form-check-input {
        flex-shrink: 0;
        margin-top: 0.125rem;
        width: 1.125rem;
        height: 1.125rem;
        border: 1px solid var(--tblr-border-color);
        transition: all 0.2s ease;
    }

    .ui-form-checkbox .form-check-input:checked {
        background-color: var(--tblr-primary);
        border-color: var(--tblr-primary);
    }

    .ui-form-checkbox .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(var(--tblr-primary-rgb), 0.1);
        border-color: var(--tblr-primary);
    }

    .ui-form-checkbox.is-invalid .form-check-input {
        border-color: var(--tblr-danger);
    }

    .ui-form-checkbox.is-invalid .form-check-input:checked {
        background-color: var(--tblr-danger);
        border-color: var(--tblr-danger);
    }

    .ui-checkbox-label {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .ui-checkbox-text {
        font-weight: 400;
        font-size: 0.875rem;
        line-height: 1.4;
        color: var(--tblr-body-color);
    }

    .ui-checkbox-description {
        font-size: 0.8125rem;
        line-height: 1.4;
        margin-top: 0.125rem;
    }

    /* Inline variant */
    .ui-form-inline {
        display: inline-flex;
        margin-right: 1rem;
        margin-bottom: 0 !important;
    }

    /* Switch variant */
    .form-switch .form-check-input {
        width: 2.5rem;
        height: 1.25rem;
        border-radius: 1rem;
        background-position: left center;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        background-size: 1rem 1rem;
        background-repeat: no-repeat;
    }

    .form-switch .form-check-input:checked {
        background-position: right center;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }

    .form-switch .form-check-input:focus {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
</style>
@endpush
@endonce
