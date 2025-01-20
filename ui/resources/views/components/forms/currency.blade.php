@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'append' => null,
    'prepend' => null,
    'hint' => null,
    'thousands_separator' => ',',
    // 'decimal_separator' => '.',
    // 'max_decimal' => 2,
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
        $($refs.{{ $ref }}).inputmask({ alias : 'currency' })
        .change(function (e) {
            $wire.set('{{ $name }}', Inputmask.unmask($refs.{{ $ref }}.value, { alias : 'currency' }));
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
