@props(['label' => null, 'active' => false])
<x-ui::tab.nav-item>
    <button type="button" {{ $attributes->class("nav-link " . ($active ? "active" : null)) }} >{{ $label }}</button>
</x-ui::tab.nav-item>
