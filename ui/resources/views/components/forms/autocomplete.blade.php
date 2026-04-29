@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'hint' => null,
])

@if ($label)
    <x-form::label :description="$description">{{ $label }}</x-form::label>
@endif

<span class="position-relative" x-data="{ show: false }">
    <input {{ $attributes->class("form-control") }}
        @focus="show = true"
        @input="show = true"
        @click="show = true"
        @blur="setTimeout(() => show = false, 200)"
    />
    <div class="list-group list-group-flush bg-light position-absolute w-100 shadow custom-scrollbar" style="z-index: 1050; max-height: 300px; overflow-y: auto;" x-show="show" x-cloak>
        {{ $slot }}
    </div>
</span>

@if ($hint)
    <small class="form-hint">{{ $hint }}</small>
@endif
