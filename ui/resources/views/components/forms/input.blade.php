@props([
    'label'             => null,
    'labelDescription' => null,
    'name'      => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'append'    => null,
    'prepend'   => null,
])

@if ($label)
    <x-form::label :description="$labelDescription">{{ $label }}</x-form::label>
@endif

@if ($prepend || $append)
    <div class="input-group">
        {{ $prepend }}
@endif

    <input {{ $attributes->class([
        "form-control",
        'is-invalid'    => $errors->has($name),
        'is-valid'      => !$errors->has($name),
    ]) }} />

@if ($prepend || $append)
        {{ $append }}
    </div>
@endif

@error($name) <div class="invalid-feedback">{{ $errors->first($name) }}</div> @enderror
