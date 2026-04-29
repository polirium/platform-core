@props([
    'variant' => 'text',
    'width' => null,
    'height' => null,
    'count' => 1,
    'avatar' => false,
    'card' => false,
])

@php
    $variantClasses = match($variant) {
        'text' => 'skeleton-text',
        'circle' => 'skeleton-circle',
        'rect' => 'skeleton-rect',
        default => 'skeleton-text',
    };

    $style = '';
    if ($width) {
        $style .= 'width: ' . (is_numeric($width) ? $width . 'px' : $width) . ';';
    }
    if ($height) {
        $style .= 'height: ' . (is_numeric($height) ? $height . 'px' : $height) . ';';
    }
@endphp

@if($card)
    <div class="skeleton-card card">
        <div class="skeleton-card-img card-img-top skeleton-rect" style="height: 200px;"></div>
        <div class="card-body">
            <div class="skeleton-rect mb-2" style="height: 24px; width: 75%;"></div>
            <div class="skeleton-rect mb-2" style="height: 16px; width: 100%;"></div>
            <div class="skeleton-rect mb-2" style="height: 16px; width: 60%;"></div>
        </div>
    </div>
@elseif($avatar)
    <div class="skeleton-avatar {{ $variantClasses }}" style="{{ $style }}"></div>
@else
    @for($i = 0; $i < $count; $i++)
        <div class="skeleton {{ $variantClasses }}" style="{{ $style }}"></div>
    @endfor
@endif

@once
@push('styles')
<style>
    /* Skeleton Loading States */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s ease-in-out infinite;
        border-radius: 0.25rem;
    }

    @keyframes skeleton-loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    .skeleton-text {
        height: 1em;
        width: 100%;
    }

    .skeleton-circle {
        border-radius: 50%;
    }

    .skeleton-rect {
        border-radius: 0.25rem;
    }

    .skeleton-avatar {
        width: 40px;
        height: 40px;
    }

    /* Dark mode support */
    [data-bs-theme="dark"] .skeleton {
        background: linear-gradient(90deg, #2a2a2a 25%, #3a3a3a 50%, #2a2a2a 75%);
        background-size: 200% 100%;
    }

    /* Card skeleton */
    .skeleton-card .skeleton-card-img {
        width: 100%;
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        .skeleton {
            animation: none;
            background: #e0e0e0;
        }

        [data-bs-theme="dark"] .skeleton {
            background: #3a3a3a;
        }
    }
</style>
@endpush
@endonce
