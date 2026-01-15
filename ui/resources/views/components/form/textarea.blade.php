@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'rows' => 4,
    'resize' => 'vertical',
    'maxlength' => null,
    'showCharCount' => false,
    'horizontal' => false,
    'labelWidth' => 'col-sm-4',
    'inputWidth' => 'col-sm-8',
    'compact' => false,
])

@php
    $hasError = $errors->has($name);
    $inputId = $attributes->get('id') ?? 'textarea-' . str_replace('_', '-', $name ?? uniqid());
    $marginClass = $compact ? 'mb-2' : 'mb-3';
@endphp

@if ($horizontal)
{{-- Horizontal Layout --}}
<div class="ui-form-group row {{ $marginClass }}" {{ $attributes->only(['x-data', 'x-init']) }}>
    @if ($label)
        <label for="{{ $inputId }}" class="{{ $labelWidth }} col-form-label ui-form-label py-0">
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    <div class="{{ $label ? $inputWidth : 'col-12' }}">
        <div class="ui-textarea-wrapper position-relative">
            <textarea
                id="{{ $inputId }}"
                rows="{{ $rows }}"
                {{ $attributes->class([
                    'ui-form-control',
                    'ui-form-textarea',
                    'form-control',
                    'form-control-sm' => $compact,
                    'is-invalid' => $hasError,
                ])->merge([
                    'disabled' => $disabled,
                    'readonly' => $readonly,
                ]) }}
            >{{ $slot }}</textarea>
            @if ($showCharCount && $maxlength)
                <div class="ui-char-count position-absolute bottom-0 end-0 p-2 small text-muted">
                    <span x-text="($el.value.length || 0)">0</span> / {{ $maxlength }}
                </div>
            @endif
        </div>
        @if ($hint)
            <p class="ui-form-hint text-muted small mt-1 mb-0">{{ $hint }}</p>
        @endif
        @if ($hasError)
            <div class="ui-form-error text-danger small mt-1">{{ $errors->first($name) }}</div>
        @endif
    </div>
</div>
@else
{{-- Vertical Layout --}}
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

    <div class="ui-textarea-wrapper position-relative">
        <textarea
            id="{{ $inputId }}"
            rows="{{ $rows }}"
            {{ $attributes->class([
                'ui-form-control',
                'ui-form-textarea',
                'form-control',
                'form-control-sm' => $compact,
                'is-invalid' => $hasError,
            ])->merge([
                'disabled' => $disabled,
                'readonly' => $readonly,
            ]) }}
        >{{ $slot }}</textarea>

        @if ($showCharCount && $maxlength)
            <div class="ui-char-count position-absolute bottom-0 end-0 p-2 small text-muted">
                <span x-text="($el.value.length || 0)">0</span> / {{ $maxlength }}
            </div>
        @endif
    </div>

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
    .ui-form-textarea {
        min-height: 100px;
        padding: 0.75rem 0.875rem;
        resize: vertical;
        line-height: 1.6;
    }

    .ui-form-textarea[rows="1"] {
        min-height: 42px;
        resize: none;
    }

    .ui-form-textarea[rows="2"] {
        min-height: 68px;
    }

    .ui-form-textarea[rows="3"] {
        min-height: 94px;
    }

    .ui-char-count {
        pointer-events: none;
        font-size: 0.75rem;
        opacity: 0.7;
    }
</style>
@endpush
@endonce
