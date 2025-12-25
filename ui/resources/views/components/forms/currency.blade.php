@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'append' => null,
    'prepend' => null,
    'hint' => null,
    'thousands_separator' => ',',
])

@php
    $ref = 'input_' . str_replace('.', '_', $name);
@endphp

<x-form::input
    :label="$label"
    :description="$description"
    :hint="$hint"
    type="text"
    {{ $attributes }}
    x-ref="{{ $ref }}"
    x-init="
        $($refs.{{ $ref }}).inputmask({
            alias : 'numeric',
            groupSeparator: '{{ $thousands_separator }}',
            autoGroup: true,
            digits: 0,
            digitsOptional: false,
            rightAlign: false,
            removeMaskOnSubmit: true
        })
        .on('change', function (e) {
            var val = Inputmask.unmask($refs.{{ $ref }}.value, { alias : 'numeric', digits: 0 });
            $wire.set('{{ $name }}', val ? parseInt(val) : 0);
        });
    "
>
    @if ($append)
        <x-slot name="append">
            {{ $append }}
        </x-slot>
    @endif
    @if ($prepend)
        <x-slot name="prepend">
            {{ $prepend }}
        </x-slot>
    @endif
</x-form::input>
