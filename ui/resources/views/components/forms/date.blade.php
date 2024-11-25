{{-- Chưa có làm :v --}}

@props([
    'label'             => null,
    'description' => null,
    'name'      => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'append'    => null,
    'prepend'   => null,
    'hint'   => null,
])

@if ($label)
    <x-form::label :description="$description">{{ $label }}</x-form::label>
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
@if ($hint)
    <small class="form-hint">{{ $hint }}</small>
@endif

@error($name) <div class="invalid-feedback">{{ $errors->first($name) }}</div> @enderror
