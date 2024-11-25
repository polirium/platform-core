@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'hint' => null,
])

@if ($label)
    <x-form::label :description="$description">{{ $label }}</x-form::label>
@endif

<span class="position-relative" x-data="{ show: 0 }">
    <input {{ $attributes->class("form-control") }} @click="show = 1" @click.away="show = 0" />
    <div class="list-group list-group-flush bg-light position-absolute w-100" x-show="show">
        {{ $slot }}
    </div>
</span>

@if ($hint)
    <small class="form-hint">{{ $hint }}</small>
@endif
