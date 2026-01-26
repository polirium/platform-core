@props(['search' => '', 'filterType' => ''])

{{-- Mobile Search Overlay --}}
<div x-data="{ showMobileSearch: false }"
     @open-mobile-search.window="showMobileSearch = true"
     x-show="showMobileSearch"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-y-[-100%]"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-[-100%]"
     class="media-mobile-search-overlay"
     role="dialog"
     aria-modal="true">

    <div class="search-container">
        {{-- Search Header --}}
        <div class="search-header">
            <button type="button" class="search-back-btn" @click="showMobileSearch = false" aria-label="{{ __('core/media::media.close') }}">
                {!! tabler_icon('arrow-left', ['class' => 'icon']) !!}
            </button>
            <div class="search-input-wrapper">
                <span class="search-icon">{!! tabler_icon('search', ['class' => 'icon icon-sm']) !!}</span>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="form-control"
                       placeholder="{{ __('core/media::media.search') }}"
                       autofocus>
            </div>
        </div>

        {{-- Filter Chips --}}
        <div class="search-filters">
            <button type="button"
                    wire:click="$set('filterType', '')"
                    class="filter-chip {{ $filterType === '' ? 'active' : '' }}">
                {{ __('core/media::media.all') }}
            </button>
            <button type="button"
                    wire:click="$set('filterType', 'image')"
                    class="filter-chip {{ $filterType === 'image' ? 'active' : '' }}">
                {{ __('core/media::media.image') }}
            </button>
            <button type="button"
                    wire:click="$set('filterType', 'video')"
                    class="filter-chip {{ $filterType === 'video' ? 'active' : '' }}">
                {{ __('core/media::media.video') }}
            </button>
            <button type="button"
                    wire:click="$set('filterType', 'document')"
                    class="filter-chip {{ $filterType === 'document' ? 'active' : '' }}">
                {{ __('core/media::media.document') }}
            </button>
            <button type="button"
                    wire:click="$set('filterType', 'audio')"
                    class="filter-chip {{ $filterType === 'audio' ? 'active' : '' }}">
                {{ __('core/media::media.audio') }}
            </button>
        </div>

        {{-- Results hint --}}
        <div class="search-results-hint">
            <p class="text-muted small mb-0">
                @if($search)
                    {{ __('core/media::media.searching_for') }}: <strong>{{ $search }}</strong>
                @else
                    {{ __('core/media::media.enter_search_term') }}
                @endif
            </p>
        </div>
    </div>
</div>
