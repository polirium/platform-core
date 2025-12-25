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
    <div {{ $prepend?->attributes->class(['input-group']) }} {{ $append?->attributes->class(['input-group']) }}>
        {{ $prepend }}
@endif

<select {{ $attributes->class([
    "form-control",
    'is-invalid'    => $errors->has($name),
    'is-valid'      => ! $errors->has($name),
]) }}
    @if ($tomselect)
        x-ref="input_tomselect"
        x-data="tomselect()"
        {{-- x-data="{
            items: $wire.entangle('{{ $name }}'),
            tomselect: new TomSelect($refs.input_tomselect, {{ $tomselectOpt }}),
        }"
        x-init="tomselect;" --}}
    @endif
>
    @if (count((array)$options) > 0)
        <option value="">-- {{ trans('Không') }} --</option>
        @foreach ((array)$options as $key => $item)
            @if (is_array($item))
                @php
                    $optionValue = $item['id'] ?? $key;
                    $optionName = $item['name'] ?? (is_string($item) ? $item : ($item['label'] ?? $optionValue));
                    $optionSelected = isset($item['selected']) && $item['selected'];
                @endphp
                <option value="{{ $optionValue }}" {{ $optionSelected ? 'selected' : '' }}>{{ $optionName }}</option>
            @else
                <option value="{{ $key }}">{{ $item }}</option>
            @endif
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

@if ($tomselect)
    @once
        @push('styles')
            <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        @endpush
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('tomselect', () => ({
                        full_select: null,
                        init(){
                            this.full_select = new TomSelect(this.$refs.input_tomselect, {{ $tomselectOpt }});
                        }
                    }))
                })
            </script>
        @endpush
    @endonce
@endif
