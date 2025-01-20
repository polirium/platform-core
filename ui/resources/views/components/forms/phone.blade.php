@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'append' => null,
    'prepend' => null,
    'hint' => null,
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
        const phone = $($refs.{{ $ref }}).inputmask({ mask : '(99) 999-999-999' })
        .change(function (e) {
            $wire.set('{{ $name }}', Inputmask.unmask($refs.{{ $ref }}.value, { mask : '(99) 999-999-999' }));
        });
    "
>
    @if ($prepend)
        <x-slot name="prepend">
            {{ $prepend }}
        </x-slot>
    @endif

    @if ($append)
        <x-slot name="append">
            {{ $append }}
        </x-slot>
    @endif
</x-form::input>
