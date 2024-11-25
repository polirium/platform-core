<{{ $tag }} {{ $attributes->merge(['class' => $class]) }}>
    @if (! empty($icon))
        {!! tabler_icon($icon) !!}
    @endif

    @if (! empty($label))
        {{ $label }}
    @else
        {{ $slot }}
    @endif
</{{ $tag }}>
