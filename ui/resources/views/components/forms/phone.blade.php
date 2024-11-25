{{-- https://alpinejs.dev/plugins/mask --}}

@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'append' => null,
    'prepend' => null,
    'hint' => null,
])

<x-form::input 
    :label="$label"
    :description="$description"
    :hint="$hint"
    type="text"
    x-data="{ value: $wire.entangle('{{ $name }}') }"
    x-model="value"
    x-mask="999 9999 9999"
    x-on:input.change="$wire.{{ $name }} = value.replaceAll(' ', '')"
>
    <x-slot name="append">
        {{ $append }}
    </x-slot>
    <x-slot name="prepend">
        {{ $prepend }}
    </x-slot>
</x-form::input>
