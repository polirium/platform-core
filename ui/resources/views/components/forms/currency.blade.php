{{-- https://alpinejs.dev/plugins/mask --}}

@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'append' => null,
    'prepend' => null,
    'hint' => null,
    'thousands_separator' => ',',
    'decimal_separator' => '.',
    'max_decimal' => 2,
])

<x-form::input 
    :label="$label"
    :description="$description"
    :hint="$hint"
    type="text"
    x-data="{ value: $wire.entangle('{{ $name }}') }"
    x-model="value"
    x-mask:dynamic="$money($input, '{{ $decimal_separator }}', '{{ $thousands_separator }}', {{ $max_decimal }})"
    x-on:input.change="$wire.{{ $name }} = value.replaceAll('{{ $thousands_separator }}', '')"
>
    <x-slot name="append">
        {{ $append }}
    </x-slot>
    <x-slot name="prepend">
        {{ $prepend }}
    </x-slot>
</x-form::input>
