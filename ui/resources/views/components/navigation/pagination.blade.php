@props([
    'paginator' => null, // Illuminate\Contracts\Pagination\LengthAwarePaginator
])

@if($paginator && $paginator->hasPages())
<nav class="pagination-wrapper" aria-label="Pagination">
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 6-6 6-6-6"/>
                        <path d="M9 18V6"/>
                    </svg>
                </span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 6-6 6-6-6"/>
                        <path d="M9 18V6"/>
                    </svg>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            @if($page == $paginator->currentPage())
                <li class="page-item active" aria-current="page">
                    <span class="page-link">{{ $page }}</span>
                </li>
            @elseif($page == 1 || $page == $paginator->lastPage() || in_array($page, [$paginator->currentPage() - 1, $paginator->currentPage() + 1]))
                <li class="page-item">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @elseif(in_array($page - 1, [$paginator->currentPage() - 1, $paginator->currentPage() + 1, $paginator->lastPage()]))
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">…</span>
                </li>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 6 6 6-6-6"/>
                        <path d="M15 18V6"/>
                    </svg>
                </a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 6 6 6-6-6"/>
                        <path d="M15 18V6"/>
                    </svg>
                </span>
            </li>
        @endif
    </ul>

    {{-- Info Text --}}
    <div class="pagination-info text-muted small">
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </div>
</nav>
@endif

@once
@push('styles')
<style>
    /* Pagination Styles */
    .pagination-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: var(--space-3);
        padding: var(--space-4) 0;
    }

    .pagination {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: var(--space-1);
    }

    .page-item {
        display: flex;
    }

    .page-link,
    .page-item span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 var(--space-2);
        font-size: var(--text-sm);
        color: var(--prof-text-primary);
        text-decoration: none;
        background: var(--prof-bg-primary);
        border: 1px solid var(--prof-border-light);
        border-radius: var(--radius-md);
        transition: all var(--duration-fast) var(--easing-out);
        cursor: pointer;
    }

    .page-item a.page-link:hover {
        background: var(--prof-bg-secondary);
        border-color: var(--prof-primary);
        color: var(--prof-primary);
    }

    .page-item.active span {
        background: var(--prof-primary);
        border-color: var(--prof-primary);
        color: white;
        font-weight: var(--font-medium);
    }

    .page-item.disabled span {
        background: var(--prof-bg-tertiary);
        color: var(--prof-text-disabled);
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pagination-info {
        font-size: var(--text-sm);
    }

    /* Dark mode */
    [data-bs-theme="dark"] .page-link,
    [data-bs-theme="dark"] .page-item span {
        background: var(--prof-gray-800);
        border-color: var(--prof-gray-700);
    }

    [data-bs-theme="dark"] .page-item a.page-link:hover {
        background: var(--prof-gray-700);
    }

    [data-bs-theme="dark"] .page-item.disabled span {
        background: var(--prof-gray-900);
    }

    /* Responsive */
    @media (max-width: 576px) {
        .pagination-wrapper {
            gap: var(--space-2);
        }

        .page-link,
        .page-item span {
            min-width: 32px;
            height: 32px;
            padding: 0 var(--space-1_5);
            font-size: var(--text-xs);
        }

        .pagination-info {
            font-size: var(--text-xs);
        }
    }
</style>
@endpush
@endonce
