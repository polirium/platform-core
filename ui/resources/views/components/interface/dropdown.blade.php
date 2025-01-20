@props([
    'label' => trans('core/base::general.action'),
    'icon' => null,
])

<div {{ $attributes->class('dropdown') }}>
    <a href="#" class="btn dropdown-toggle" data-bs-toggle="dropdown">
        @if ($icon)
            {!! tabler_icon($icon) !!}
        @endif
        {{ $label }}
    </a>
    <div class="dropdown-menu">
        {{ $slot }}
    </div>
</div>
