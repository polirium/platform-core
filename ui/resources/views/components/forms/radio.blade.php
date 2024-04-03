@props([
    'label' => null,
    'name'  => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
])

<label class="form-check">
    <input type="radio" {{ $attributes->class([
        "form-check-input",
        'is-invalid'    => $errors->has($name),
        'is-valid'      => !$errors->has($name),
    ])->merge(["name" => $name]) }}
    />
    <span class="form-check-label">{{ $label }}</span>
</label>

@error($name) <div class="invalid-feedback">{{ $errors->first($name) }}</div> @enderror
