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
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for modal events to manage aria-hidden correctly
        document.addEventListener('show.bs.modal', function(e) {
            // Remove aria-hidden immediately when modal starts showing
            e.target.removeAttribute('aria-hidden');
        });

        document.addEventListener('shown.bs.modal', function(e) {
            // Ensure aria-hidden is removed and aria-modal is set
            e.target.removeAttribute('aria-hidden');
            e.target.setAttribute('aria-modal', 'true');
        });

        document.addEventListener('hide.bs.modal', function(e) {
            // Move focus out before aria-hidden is set
            const activeElement = document.activeElement;
            if (e.target.contains(activeElement)) {
                activeElement.blur();
            }
        });

        document.addEventListener('hidden.bs.modal', function(e) {
            // Set aria-hidden after modal is fully hidden
            e.target.setAttribute('aria-hidden', 'true');
            e.target.removeAttribute('aria-modal');

            // Force cleanup backdrop and body - runs after each modal closes
            setTimeout(function() {
                var backdrops = document.querySelectorAll('.modal-backdrop');
                var openModals = document.querySelectorAll('.modal.show');

                // Remove all extra backdrops
                for (var i = openModals.length; i < backdrops.length; i++) {
                    if (backdrops[i]) backdrops[i].remove();
                }

                // If no modals open, cleanup everything
                if (openModals.length === 0) {
                    // Remove all remaining backdrops
                    document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                }
            }, 100);
        });
    });
</script>
@endpush
@endonce
