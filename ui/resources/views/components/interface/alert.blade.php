@props([
    'color' => 'primary',
    'icon' => null,
    'label' => null,
    'dismiss' => false,
    'important' => false,
])

<div {{ $attributes->merge([
        'class' => "alert alert-{$color} " . ($dismiss ? ' alert-dismissible ' : '') . ($important ? ' alert-important ' : ''),
        'role' => 'alert',
    ]) }}>
    <div class="d-flex">
        @if ($icon)
            <div>{!! tabler_icon($icon) !!}</div>
        @endif
        <div>
            @if ($label)
                {{ $label }}
            @else
                {{ $slot }}
            @endif
        </div>
    </div>
    @if ($dismiss)
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    @endif
</div>
