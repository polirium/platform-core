@props([
    'color' => 'primary',
    'icon' => null,
    'label' => null,
])

<div {{ $attributes->class(['card', 'card card-sm']) }}>
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <span class="bg-{{ $color }} text-white avatar">
                    {!! tabler_icon($icon) !!}
                </span>
            </div>
            <div class="col">
                {{ $label ?: $slot }}
            </div>
        </div>
    </div>
</div>
