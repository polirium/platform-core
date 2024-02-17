<div class="{{ $cardClasses }}">
    @if ($header)
        {{ $header }}
    @elseif ($title || $action)
        <div class="{{ $headerClasses }}">
            <h3 class="card-title">{{ $title }}</h3>

            @if ($action)
                <div class="card-actions">
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif

    <div {{ $attributes->merge(['class' => "{$padding}"]) }}>
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="{{ $footerClasses }}">
            {{ $footer }}
        </div>
    @endif
</div>
