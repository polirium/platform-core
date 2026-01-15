@props([
    'tabs' => [],
    'activeTab' => null,
])

@php
    if (empty($tabs)) {
        $tabs = [];
    }
    $activeTab ??= $tabs[array_key_first($tabs)] ?? null;
@endphp

<div class="ui-form-tabs" x-data="{ activeTab: '{{ $activeTab }}' }">
    {{-- Tab Navigation --}}
    <div class="ui-form-tabs-nav">
        <div class="d-flex gap-1" role="tablist">
            @foreach ($tabs as $key => $tab)
                <button
                    type="button"
                    class="ui-form-tab-btn"
                    :class="{ 'active': activeTab === '{{ $key }}' }"
                    @click="activeTab = '{{ $key }}'"
                    role="tab"
                    :aria-selected="activeTab === '{{ $key }}' ? 'true' : 'false'"
                    :aria-controls="'panel-{{ $key }}'"
                >
                    @if (isset($tab['icon']) && $tab['icon'])
                        <span class="me-2">
                            {!! tabler_icon($tab['icon'], ['class' => 'icon']) !!}
                        </span>
                    @endif

                    <span>{{ $tab['label'] ?? $tab }}</span>

                    @if (isset($tab['badge']) && $tab['badge'])
                        <span class="badge bg-primary ms-2">{{ $tab['badge'] }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Tab Content --}}
    <div class="ui-form-tabs-content mt-4">
        @foreach ($tabs as $key => $tab)
            <div
                x-show="activeTab === '{{ $key }}'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                id="panel-{{ $key }}"
                role="tabpanel"
                {{ $activeTab !== $key ? 'style="display: none;"' : '' }}
            >
                {{ $slot }}
            </div>
        @endforeach
    </div>
</div>

@once
@push('styles')
<style>
    .ui-form-tabs-nav {
        border-bottom: 1px solid var(--tblr-border-color);
        margin: -1.25rem -1.25rem 0 -1.25rem;
        padding: 0 1.25rem;
    }

    .ui-form-tab-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--tblr-muted);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .ui-form-tab-btn:hover {
        color: var(--tblr-body-color);
        background-color: var(--tblr-bg-muted);
    }

    .ui-form-tab-btn.active {
        color: var(--tblr-primary);
        border-bottom-color: var(--tblr-primary);
    }

    .ui-form-tabs-content > div:not([style*="display: none"]) {
        animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(0.5rem);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Tab badge */
    .ui-form-tab-btn .badge {
        font-size: 0.6875rem;
        padding: 0.125rem 0.375rem;
        font-weight: 600;
    }
</style>
@endpush
@endonce
