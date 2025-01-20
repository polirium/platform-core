@props([
    'color' => 'primary',
    'value' => 0,
])

<div class="progress progress-xs">
    <div {{ $attributes->merge([
        'class' => "progress-bar bg-{$color}",
        'style' => "width: {$value}%",
    ]) }}>{{ $value }}%</div>
</div>
