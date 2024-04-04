@props([
    'label' => null,
    'name'  => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
])

<label class="form-check">
    <input type="checkbox" {{ $attributes->class([
        "form-check-input",
        'is-invalid'    => $errors->has($name),
        'is-valid'      => !$errors->has($name),
    ]) }}
        x-data="{ checked: $wire.entangle('{{ $name }}') }"
        x-bind:checked="checked"
    />
    @if ($label)
        <span class="form-check-label">{{ $label }}</span>
    @endif
</label>

@error($name) <div class="invalid-feedback">{{ $errors->first($name) }}</div> @enderror
