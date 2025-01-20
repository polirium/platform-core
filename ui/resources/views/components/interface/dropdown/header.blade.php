@props([
    'label' => null,
])

<span {{ $attributes->class('dropdown-header') }}>
    @if ($label)
        {{ $label }}
    @else
        {{ $slot }}
    @endif
</span>
