@props([
    'header' => null,
    'body' => null,
    'footer' => null,
    'action' => null,
])

<div {{ $attributes->class('card') }}>
    @if ($header)
        @if (is_string($header))
            <div class="card-header">
                <h3 class="card-title">{{ $header }}</h3>
                @if ($action)
                    <div class="card-actions">{{ $action }}</div>
                @endif
            </div>
        @else
            <div {{ $header->attributes?->class('card-header') ?? 'class=card-header' }}>
                {{ $header }}

                @if ($action)
                    <div class="card-actions">{{ $action }}</div>
                @endif
            </div>
        @endif
    @endif

    @if ($body)
        <div {{ $body->attributes?->class('card-body') ?? 'class=card-body' }}>{{ $body }}</div>
    @else
        <div class="card-body">{{ $slot }}</div>
    @endif

    @if ($footer)
        <div {{ $footer->attributes?->class('card-footer') ?? 'class=card-footer' }}>
            {{ $footer }}
        </div>
    @endif
</div>
