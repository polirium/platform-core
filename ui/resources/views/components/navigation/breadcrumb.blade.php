@props([
    'items' => [], // [['label' => 'Home', 'url' => '/'], ['label' => 'Users', 'url' => '/users']]
    'home' => ['label' => 'Home', 'url' => '/'],
])

@php
    // Add home item if not present
    if (!empty($items) && ($items[0]['label'] ?? null) !== ($home['label'] ?? 'Home')) {
        array_unshift($items, $home);
    }
@endphp

@if(!empty($items) && count($items) > 1)
<nav class="breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb-list">
        @foreach($items as $index => $item)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $item['label'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @else
                        <span>{{ $item['label'] }}</span>
                    @endif
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif

@once
@push('styles')
<style>
    /* Breadcrumb Styles */
    .breadcrumb {
        padding: var(--space-2) 0;
        margin-bottom: var(--space-4);
    }

    .breadcrumb-list {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
        font-size: var(--text-sm);
        color: var(--prof-text-secondary);
    }

    .breadcrumb-item:not(:last-child)::after {
        content: '/';
        display: inline-block;
        margin: 0 var(--space-2);
        color: var(--prof-gray-400);
    }

    .breadcrumb-item a {
        color: var(--prof-text-secondary);
        text-decoration: none;
        transition: color var(--duration-fast) var(--easing-out);
    }

    .breadcrumb-item a:hover {
        color: var(--prof-primary);
    }

    .breadcrumb-item.active {
        color: var(--prof-text-primary);
        font-weight: var(--font-medium);
    }

    /* Dark mode */
    [data-bs-theme="dark"] .breadcrumb-item:not(:last-child)::after {
        color: var(--prof-gray-500);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .breadcrumb {
            padding: var(--space-1) 0;
        }

        .breadcrumb-item {
            font-size: var(--text-xs);
        }
    }
</style>
@endpush
@endonce
