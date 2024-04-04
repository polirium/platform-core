@props([
    'header' => null,
    'footer' => null,
])

<div class="card">
    <div class="card-header">
        <ul {{ $attributes->class("nav nav-tabs card-header-tabs") }}>
            {{ $header }}
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            {{ $slot }}
        </div>
    </div>
    <div class="card-footer">
        {{ $footer }}
    </div>
</div>
