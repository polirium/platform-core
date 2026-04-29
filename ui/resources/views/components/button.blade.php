@php
    // Remove btn-icon class if slot has content (btn-icon is only for icon-only buttons)
    $hasContent = ! empty($label) || (isset($slot) && ! $slot->isEmpty());
    $finalClass = $hasContent ? str_replace('btn-icon', '', $class) : $class;
    $finalClass = preg_replace('/\s+/', ' ', trim($finalClass)); // Clean up extra spaces
@endphp
<{{ $tag }} {{ $attributes->merge(['class' => $finalClass]) }}>
    @if (! empty($icon))
        {!! tabler_icon($icon, ['class' => 'icon']) !!}
    @endif

    @if (! empty($label))
        {{ $label }}
    @else
        {{ $slot }}
    @endif
</{{ $tag }}>
