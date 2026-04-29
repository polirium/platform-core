@props(['id' => null])
<div id="{{ $id }}" {{ $attributes->class("accordion") }} wire:ignore.self>
    {{ $slot }}
</div>
