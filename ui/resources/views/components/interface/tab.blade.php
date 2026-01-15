@props([
    'header' => null,
    'footer' => null,
])

<div {{ $attributes->class("card") }}>
    @if ($header)
        <div class="card-header">
            <ul {{ $header->attributes?->class("nav nav-tabs card-header-tabs w-100") ?? 'class=nav nav-tabs card-header-tabs w-100' }}>
                {{ $header }}
            </ul>
        </div>
    @endif
    <div class="card-body">
        <div class="tab-content">
            {{ $slot }}
        </div>
    </div>
    @if ($footer)
        <div {{ $footer->attributes?->class("card-footer") ?? 'class=card-footer' }}>{{ $footer }}</div>
    @endif
</div>
