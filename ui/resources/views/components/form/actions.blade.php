@props([
    'align' => 'right', // left, center, right, justify
])

<div class="ui-form-actions d-flex align-items-center gap-2 mt-4 pt-3 {{ $align === 'left' ? 'justify-content-start' : ($align === 'center' ? 'justify-content-center' : ($align === 'justify' ? 'justify-content-between' : 'justify-content-end')) }}">
    {{ $slot }}
</div>

@once
@push('styles')
<style>
    .ui-form-actions {
        border-top: 1px solid var(--tblr-border-color);
        padding-top: 1rem;
    }

    .ui-form-actions .btn {
        min-height: 38px;
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .ui-form-actions .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endpush
@endonce
