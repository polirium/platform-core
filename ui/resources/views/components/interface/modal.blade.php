@props([
    'id' => null,
    'header' => null,
    'footer' => null,
    'keyboard' => 'true',
    'backdrop' => 'normal',
])

{{-- Accessibility: Bootstrap 5 manages aria-hidden automatically --}}
<div
    class="modal modal-blur fade"
    id="{{ $id }}"
    tabindex="-1"
    role="dialog"
    wire:ignore.self
    data-bs-backdrop="{{ $backdrop }}"
    data-bs-keyboard="{{ $keyboard }}"
>
    <div {{ $attributes->class('modal-dialog') }}>
        <div class="modal-content">
            @if ($header)
                @if (is_string($header))
                    <div class="modal-header">
                        <h5 class="modal-title" id="{{ $id }}-title">{{ $header }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                @else
                    <div {{ $header->attributes?->class('modal-header') }}>
                        {{ $header }}
                    </div>
                @endif
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

@once
@push('scripts')
<script>
    // Fix Bootstrap Modal aria-hidden warning
    // The issue: Bootstrap sets aria-hidden before focus is moved out
        // Robust cleanup using MutationObserver
        // Watches for changes in body class or child list to ensure consistent state
        const observer = new MutationObserver(function(mutations) {
            const openModals = document.querySelectorAll('.modal.show');
            const backdrops = document.querySelectorAll('.modal-backdrop');
            const bodyHasModalOpen = document.body.classList.contains('modal-open');

            // Scenario 1: No open modals, but body has modal-open class -> FIX IT
            if (openModals.length === 0 && bodyHasModalOpen) {
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }

            // Scenario 2: No open modals, but backdrops exist -> REMOVE THEM
            if (openModals.length === 0 && backdrops.length > 0) {
                backdrops.forEach(b => b.remove());
            }

            // Scenario 3: Mismatch between open modals and backdrops -> SYNC THEM
            if (backdrops.length > openModals.length) {
                // Remove oldest backdrops first (usually they are appended to end, but simpler to remove count diff)
                // We keep only as many backdrops as open modals
                for (let i = 0; i < (backdrops.length - openModals.length); i++) {
                    if (backdrops[i]) backdrops[i].remove();
                }
            }
        });

        observer.observe(document.body, {
            attributes: true,
            childList: true,
            subtree: false,
            attributeFilter: ['class']
        });
    });
</script>
@endpush
@endonce
