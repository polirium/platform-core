@props(['show' => false])

@if ($show)
    <div {{ $attributes->class("tab-pane active show") }}>{{ $slot }}</div>
@endif
