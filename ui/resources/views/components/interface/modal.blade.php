@props([
    'id' => null,
    'header' => null,
    'footer' => null,
    'keyboard' => 'true',
    'backdrop' => 'normal',
])

<div class="modal modal-blur fade" id="{{ $id }}" tabindex="-1" style="display: none;" aria-modal="false" aria-hidden="true" role="dialog" wire:ignore.self data-bs-backdrop="{{ $backdrop }}" data-bs-keyboard="{{ $keyboard }}">
    <div {{ $attributes->class('modal-dialog') }} role="document">
        <div class="modal-content">
            @if (is_string($header))
                <div class="modal-header">
                    <h5 class="modal-title">{{ $header }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            @else
                <div {{ $header->attributes?->class('modal-header') }}>
                    {{ $header }}
                </div>
            @endif
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if ($footer)
                <div {{ $footer->attributes?->class('modal-footer') }}>
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
