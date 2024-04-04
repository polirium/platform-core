@props(['label' => null, 'active' => false])
<li class="nav-item">
    <button type="button" {{ $attributes->class("nav-link " . ($active ? "active" : null)) }} >{{ $label }}</button>
</li>
