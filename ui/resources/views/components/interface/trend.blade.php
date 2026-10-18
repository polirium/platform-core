{{--
    Change against the previous period: green up, red down, grey when flat
    or when there is nothing to compare with.

    <x-ui::trend :value="12.5" />          shows "+12,5%" in green
    <x-ui::trend :value="-3" inverse />    for costs: a decrease is shown in green
--}}
@props([
    'value' => null,
    'inverse' => false,
])

@php
    $good = $value !== null && ($inverse ? $value < 0 : $value > 0);
    $bad = $value !== null && ($inverse ? $value > 0 : $value < 0);
    $class = $good ? 'text-success' : ($bad ? 'text-danger' : 'text-secondary');
    $icon = $value === null || $value == 0 ? 'minus' : ($value > 0 ? 'trending-up' : 'trending-down');
    $label = $value === null ? __('core/base::general.trend_new') : (($value > 0 ? '+' : '') . rtrim(rtrim(number_format($value, 1, ',', '.'), '0'), ',') . '%');
@endphp

<span {{ $attributes->merge(['class' => "ui-trend d-inline-flex align-items-center gap-1 small fw-medium {$class}"]) }}
      title="{{ __('core/base::general.trend_hint') }}">
    {!! tabler_icon($icon, ['class' => 'icon icon-sm']) !!}{{ $label }}
</span>
