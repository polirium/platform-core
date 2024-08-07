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
<span
    x-data="{ value: @entangle($attributes->wire('model')), autonumeric: null }"
    x-init="
        autonumeric = new AutoNumeric($refs.currency, value, {{ $autoNumericOpt }});
    "
>
    @if ($prepend || $append)
        <div class="input-group">
            {{ $prepend }}
    @endif

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

    @if ($prepend || $append)
            {{ $append }}
        </div>
    @endif

    @error($name) <div class="invalid-feedback">{{ $errors->first($name) }}</div> @enderror
</span>
