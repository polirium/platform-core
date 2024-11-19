@props([
    'header' => null,
    'body' => null,
    'footer' => null,
    'action' => null,
])

<div {{ $attributes->class('card') }}>
    @isset($header)
        @if (!is_html($header))
            <div class="card-header">
                <h3 class="card-title">{{ $header }}</h3>
                @if ($action)
                    <div class="card-actions">{{ $action }}</div>
                @endif
            </div>
        @else
            <div {{ $header->attributes->class('card-header') }}>
                {{ $header }}

                @if ($action)
                    <div class="card-actions">{{ $action }}</div>
                @endif
            </div>
        @endif
    @endisset

    @isset($body)
        <div {{ $body->attributes->class('card-body') }}>{{ $body }}</div>
    @else
        <div class="card-body">{{ $slot }}</div>
    @endisset

    @isset($footer)
        <div {{ $footer->attributes->class('card-footer') }}>
            {{ $footer }}
        </div>
    @endisset
</div>
