@props([
    'label'             => null,
    'description'  => null,
    'name'      => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'append'    => null,
    'prepend'   => null,
    'tomselect' => null,
    'tomselectOpt' => "{}",
    'options'   => [],
    'hint'   => null,
])

@if ($label)
    <x-form::label :description="$description">{{ $label }}</x-form::label>
@endif
@if ($prepend || $append)
    <div class="input-group">
        {{ $prepend }}
@endif

<select {{ $attributes->class([
    "form-control",
    'is-invalid'    => $errors->has($name),
    'is-valid'      => !$errors->has($name),
]) }}
    @if ($tomselect)
        x-ref="input_tomselect"
        x-data="{
            items: $wire.entangle('{{ $name }}'),
            tomselect: new TomSelect($refs.input_tomselect, {{ $tomselectOpt }}),
        }"
        x-init="tomselect;"
    @endif
>
    @if (count($options) > 0)
        <option value="">-- {{ trans('Không') }} --</option>
        @foreach ($options as $key => $item)
            <option value="{{ isset($item['id']) ? $item['id'] : $key }}">{{ isset($item['name']) ? $item['name'] : $item }}</option>
        @endforeach
    @else
        {{ $slot }}
    @endif
</select>

@if ($prepend || $append)
        {{ $append }}
    </div>
@endif

@if ($hint)
    <small class="form-hint">{{ $hint }}</small>
@endif

@error($name) <div class="invalid-feedback">{{ $errors->first($name) }}</div> @enderror
