@props([
    'header' => null,
    'footer' => null,
])

<div {{ $attributes->class("card") }}>
    @if ($header)
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
    @endif
    <div class="card-body">{{ $slot }}</div>
    @if ($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>