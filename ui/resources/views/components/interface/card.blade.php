@props([
    'header' => null,
    'footer' => null,
    'action' => null,
])

<div {{ $attributes->class("card") }}>
    @if ($header)
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
            @if ($action)
                <div class="card-actions">{{ $action }}</div>
            @endif
        </div>
    @endif
    <div class="card-body">{{ $slot }}</div>
    @if ($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
