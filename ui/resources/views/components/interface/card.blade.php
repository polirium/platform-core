@props([
    'header' => null,
    'footer' => null,
    'action' => null,
])

<div {{ $attributes->class("card") }}>
    @if ($header)
        @if (is_string($header))
            <div class="card-header">
                <h3 class="card-title">{{ $header }}</h3>
                @if ($action)
                    <div class="card-actions">{{ $action }}</div>
                @endif
            </div>
        @else
            <div {{ $header?->attributes?->class("card-header") }}>
                {{ $header }}
            </div>
        @endif
    @endif
    <div class="card-body">{{ $slot }}</div>
    @if ($footer)
        <div {{ $footer?->attributes?->class("card-footer") }}>
            {{ $footer }}
        </div>
    @endif
</div>
