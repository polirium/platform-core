@props([
    'icon' => null,
    'label' => null,
])

<button {{ $attributes->merge([
    'class' => 'dropdown-item',
]) }}>
    @if ($icon)
        {!! tabler_icon($icon) !!}
    @endif
    @if ($label)
        {{ $label }}
    @else
        {{ $slot }}
    @endif
</button>
