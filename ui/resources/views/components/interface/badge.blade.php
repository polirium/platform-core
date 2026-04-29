@props([
    'color' => 'blue',
    'label' => null,
    'outline' => null,
    'lt' => null,
])

@php
    if ($outline) {
        $class = "badge badge-outline text-{$color}";
    } elseif ($lt) {
        $class = "badge bg-{$color}-lt";
    } else {
        $class = "badge bg-{$color} text-{$color}-fg";
    }
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>
    @if ($label)
        {{ $label }}
    @else
        {{ $slot }}
    @endif
</span>
