@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'format' => 'd/m/Y',
    'min' => null,
    'max' => null,
    'no_calendar' => false,
    'enable_time' => false,
    'hint' => null,
])

@php
    $ref = 'datepicker_' . str_replace('.', '_', $name);
@endphp

@if ($label)
    <x-form::label :description="$description">{{ $label }}</x-form::label>
@endif

<input
    type="text"
    {{ $attributes->class([
        "form-control",
        'is-invalid' => $errors->has($name),
    ])->whereDoesntStartWith('wire:model') }}
    x-data="{ pickr: null }"
    x-ref="{{ $ref }}"
    x-init="
        if (typeof flatpickr !== 'undefined') {
            pickr = flatpickr($refs.{{ $ref }}, {
                dateFormat: '{{ $format }}',
                @if($min) minDate: '{{ $min }}', @endif
                @if($max) maxDate: '{{ $max }}', @endif
                @if($no_calendar) noCalendar: true, @endif
                @if($enable_time) enableTime: true, @endif
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        const formattedDate = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                        $wire.set('{{ $name }}', formattedDate);
                    }
                }
            });
        }
    "
/>

@if ($hint)
    <small class="form-hint">{{ $hint }}</small>
@endif

@error($name) <div class="invalid-feedback">{{ $errors->first($name) }}</div> @enderror

@once
    @push('scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" onerror="this.onerror=null;this.remove()">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr" onerror="this.onerror=null;this.remove()"></script>
    @endpush
@endonce
