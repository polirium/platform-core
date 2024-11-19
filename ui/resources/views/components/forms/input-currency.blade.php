@props([
    'label'             => null,
    'labelDescription' => null,
    'name'      => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'append'    => null,
    'prepend'   => null,
    // 'autoNumericOpt' => "{
    //     currencySymbol: '₫ ',
    //     decimalCharacter: ',',
    //     digitGroupSeparator: '.'
    // }"
    'autoNumericOpt' => "{}"
])

@if ($label)
    <x-form::label :description="$labelDescription">{{ $label }}</x-form::label>
@endif

<div @class([
    "input-group" => $prepend || $append,
])
    x-data="{ value: @entangle($attributes->wire('model')), autonumeric: undefined }"
    x-init="
        autonumeric = new AutoNumeric($refs.currency, value, {{ $autoNumericOpt }});
    "
>
    {{ $prepend }}

    <input {{ $attributes->class([
        "form-control",
        'is-invalid'    => $errors->has($name),
        'is-valid'      => !$errors->has($name),
    ]) }} 
            type="text" 
            x-ref="currency" 
            x-on:blur="value = autonumeric.getNumber()" 
            x-bind="value ?? {}"
            />

    {{ $append }}
</div>

@error($name) <div class="invalid-feedback">{{ $errors->first($name) }}</div> @enderror
