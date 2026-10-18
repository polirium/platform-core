{{--
    Small trend chart drawn from real values.

    <x-ui::sparkline :values="[3, 5, 2, 8]" type="area" color="success" />

    type: line | area | bar. color: a Tabler colour (primary, success, info, danger...).
--}}
@props([
    'values' => [],
    'type' => 'line',
    'color' => 'primary',
    'height' => 32,
])

@php
    $values = array_values(array_map('floatval', (array) $values));
    $count = count($values);
    $max = $count ? max($values) : 0;
    $width = 100;
    $chartHeight = 30;
    $pad = 2;
    $y = fn ($value) => $max > 0 ? round($chartHeight - $pad - ($value / $max) * ($chartHeight - 2 * $pad), 2) : $chartHeight - $pad;
    $x = fn ($index) => $count > 1 ? round($index * $width / ($count - 1), 2) : $width / 2;
    $points = collect($values)->map(fn ($value, $index) => $x($index) . ',' . $y($value))->implode(' ');
    $gradient = 'spark-' . \Illuminate\Support\Str::random(8);
@endphp

<div {{ $attributes->merge(['class' => 'ui-sparkline']) }} style="height: {{ (int) $height }}px" aria-hidden="true">
    @if ($count === 0)
        <div class="ui-sparkline-empty"></div>
    @else
        <svg viewBox="0 0 {{ $width }} {{ $chartHeight }}" preserveAspectRatio="none" focusable="false">
            @if ($type === 'bar')
                @php $barWidth = max(1, ($width / $count) * 0.62); @endphp
                @foreach ($values as $index => $value)
                    @php $barX = ($index + 0.5) * $width / $count - $barWidth / 2; $top = $y($value); @endphp
                    <rect x="{{ round($barX, 2) }}" y="{{ $top }}" width="{{ round($barWidth, 2) }}" height="{{ max(0.8, $chartHeight - $pad - $top) }}" rx="0.8"
                          style="fill: var(--tblr-{{ $color }}); opacity: {{ $index === $count - 1 ? 1 : 0.45 }}" />
                @endforeach
            @else
                @if ($type === 'area')
                    <defs>
                        <linearGradient id="{{ $gradient }}" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" style="stop-color: var(--tblr-{{ $color }}); stop-opacity: 0.28" />
                            <stop offset="100%" style="stop-color: var(--tblr-{{ $color }}); stop-opacity: 0" />
                        </linearGradient>
                    </defs>
                    <polygon points="0,{{ $chartHeight }} {{ $points }} {{ $width }},{{ $chartHeight }}" fill="url(#{{ $gradient }})" />
                @endif
                <polyline points="{{ $points }}" fill="none" vector-effect="non-scaling-stroke"
                          style="stroke: var(--tblr-{{ $color }}); stroke-width: 2; stroke-linejoin: round; stroke-linecap: round" />
            @endif
        </svg>
    @endif
</div>
