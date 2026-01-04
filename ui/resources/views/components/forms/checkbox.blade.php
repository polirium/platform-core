@props([
    'label' => null,
    'name'  => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'hint'  => null,
])

<label class="form-check">
    <input type="checkbox" {{ $attributes->class([
        "form-check-input",
        'is-invalid'    => $errors->has($name),
    ]) }} />
    @if ($label)
        <span class="form-check-label ms-2">{{ $label }}</span>
    @endif

    @if ($hint)
        <small class="form-hint">{{ $hint }}</small>
    @endif
</label>

@error($name) <div class="invalid-feedback">{{ $errors->first($name) }}</div> @enderror
