@props([
    'icon' => null,
    'title' => null,
    'description' => null,
    'actionText' => null,
    'actionUrl' => null,
    'compact' => false,
])

<div class="empty-state {{ $compact ? 'empty-state-compact' : '' }}">
    @if($icon)
        <div class="empty-state-icon">
            {!! $icon }
        </div>
    @endif

    @if($title)
        <h4 class="empty-state-title">{{ $title }}</h4>
    @endif

    @if($description)
        <p class="empty-state-description">{{ $description }}</p>
    @endif

    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="empty-state-action btn btn-primary">
            {{ $actionText }}
        </a>
    @endif
</div>

@once
@push('styles')
<style>
    /* Empty State Styles */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: var(--space-12) var(--space-6);
        text-align: center;
        min-height: 300px;
    }

    .empty-state-compact {
        padding: var(--space-6) var(--space-4);
        min-height: 150px;
    }

    .empty-state-icon {
        width: 64px;
        height: 64px;
        margin-bottom: var(--space-4);
        color: var(--prof-gray-300);
        opacity: 0.8;
    }

    .empty-state-icon svg {
        width: 100%;
        height: 100%;
    }

    .empty-state-title {
        margin: 0 0 var(--space-2) 0;
        font-size: var(--text-lg);
        font-weight: var(--font-semibold);
        color: var(--prof-text-primary);
    }

    .empty-state-description {
        margin: 0 0 var(--space-6) 0;
        font-size: var(--text-base);
        color: var(--prof-text-secondary);
        max-width: 400px;
    }

    .empty-state-action {
        min-width: 150px;
    }

    /* Compact variant */
    .empty-state-compact .empty-state-icon {
        width: 48px;
        height: 48px;
    }

    .empty-state-compact .empty-state-title {
        font-size: var(--text-base);
    }

    .empty-state-compact .empty-state-description {
        font-size: var(--text-sm);
    }

    /* Dark mode */
    [data-bs-theme="dark"] .empty-state-icon {
        color: var(--prof-gray-600);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .empty-state {
            padding: var(--space-8) var(--space-4);
        }

        .empty-state-icon {
            width: 48px;
            height: 48px;
        }

        .empty-state-title {
            font-size: var(--text-base);
        }

        .empty-state-description {
            font-size: var(--text-sm);
        }
    }
</style>
@endpush
@endonce
