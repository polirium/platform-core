@php
    // Helper để lấy tên từ relation (có thể là object hoặc array)
    $getRelationName = function($relation) {
        if (!$relation) return null;
        if (is_array($relation)) return $relation['name'] ?? null;
        return $relation->name ?? null;
    };
@endphp

<div class="professional-detail-wrapper" x-data="{ activeTab: 1 }">
    {{-- Custom Tabs with Alpine.js --}}
    <div class="professional-tabs">
        <ul class="professional-tabs-header">
            <li class="professional-tab-item" :class="{ 'active': activeTab === 1 }">
                <button type="button" @click="activeTab = 1" class="professional-tab-button">
                    {{ __('core/base::general.information') }}
                </button>
            </li>
            <li class="professional-tab-item" :class="{ 'active': activeTab === 2 }">
                <button type="button" @click="activeTab = 2" class="professional-tab-button">
                    {{ __('core/base::general.users') }}
                </button>
            </li>
            <li class="professional-tab-item" :class="{ 'active': activeTab === 3 }">
                <button type="button" @click="activeTab = 3" class="professional-tab-button">
                    {{ __('core/base::general.taking_addresses') }}
                </button>
            </li>
        </ul>

        <div class="professional-tabs-content">
            {{-- Tab 1: Information --}}
            <div x-show="activeTab === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="professional-info-section">

                <div class="row g-3">
                    {{-- Left Column --}}
                    <div class="col-md-6">
                        <div class="professional-info-card">
                            <div class="professional-info-icon-wrapper primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 21h18"/>
                                    <path d="M9 8h1"/>
                                    <path d="M9 12h1"/>
                                    <path d="M5 21V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14"/>
                                </svg>
                            </div>
                            <div class="professional-info-content">
                                <span class="professional-info-label">{{ __('core/base::general.branch_name') }}</span>
                                <span class="professional-info-value">{{ $row->name }}</span>
                            </div>
                        </div>

                        <div class="professional-info-card">
                            <div class="professional-info-icon-wrapper success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                            </div>
                            <div class="professional-info-content">
                                <span class="professional-info-label">{{ __('core/base::general.phone') }}</span>
                                <span class="professional-info-value">{{ $row->phone }}{{ $row->phone_2 ? ' - ' . $row->phone_2 : '' }}</span>
                            </div>
                        </div>

                        <div class="professional-info-card">
                            <div class="professional-info-icon-wrapper info">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </div>
                            <div class="professional-info-content">
                                <span class="professional-info-label">{{ __('core/base::general.email') }}</span>
                                <span class="professional-info-value">{{ $row->email ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="col-md-6">
                        <div class="professional-info-card">
                            <div class="professional-info-icon-wrapper warning">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>
                            <div class="professional-info-content">
                                <span class="professional-info-label">{{ __('core/base::general.address') }}</span>
                                <span class="professional-info-value">{{ $row->address ?: '-' }}</span>
                            </div>
                        </div>

                        <div class="professional-info-card">
                            <div class="professional-info-icon-wrapper neutral">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>
                            <div class="professional-info-content">
                                <span class="professional-info-label">{{ __('core/base::general.delivery_area') }}</span>
                                <span class="professional-info-value">
                                    @php
                                        $provinceName = $getRelationName($row->province);
                                        $districtName = $getRelationName($row->district);
                                    @endphp
                                    @if($provinceName && $districtName)
                                        {{ $provinceName }} {{ $districtName }}
                                    @elseif($provinceName)
                                        {{ $provinceName }}
                                    @elseif($districtName)
                                        {{ $districtName }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="professional-info-card">
                            <div class="professional-info-icon-wrapper neutral">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </div>
                            <div class="professional-info-content">
                                <span class="professional-info-label">{{ __('core/base::general.ward_commune') }}</span>
                                <span class="professional-info-value">{{ $getRelationName($row->ward) ?: '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            {{-- Tab 2: Users --}}
            <div x-show="activeTab === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
            <div class="professional-users-section">
                @if ($row->users && is_countable($row->users) && count($row->users) > 0)
                    <div class="professional-user-list">
                        @foreach ($row->users as $item)
                            @php
                                $userName = is_array($item) ? ($item['name'] ?? '') : ($item->name ?? '');
                                $userEmail = is_array($item) ? ($item['email'] ?? null) : ($item->email ?? null);
                                $firstChar = $userName ? strtoupper(substr($userName, 0, 1)) : 'U';
                            @endphp
                            <div class="professional-user-item">
                                <div class="professional-user-avatar {{ $row->status ? 'active' : 'inactive' }}">
                                    {{ $firstChar }}
                                </div>
                                <div class="professional-user-info">
                                    <span class="professional-user-name">{{ $userName }}</span>
                                    @if ($userEmail)
                                        <span class="professional-user-email">{{ $userEmail }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            </div>

            {{-- Tab 3: Taking Addresses --}}
            <div x-show="activeTab === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
            <div class="professional-addresses-section">
                @php
                    if (method_exists($row, 'takingAddresses')) {
                        $addresses = $row->takingAddresses()->paginate(10);
                    } elseif (isset($row->takingAddresses) && is_iterable($row->takingAddresses)) {
                        $addresses = collect($row->takingAddresses);
                    } else {
                        $addresses = collect([]);
                    }
                @endphp

                @if ($addresses instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $addresses->count() > 0)
                    <div class="professional-address-list">
                        @foreach ($addresses as $item)
                            <div class="professional-address-card">
                                <div class="professional-address-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                </div>
                                <div class="professional-address-content">
                                    <p class="professional-address-text mb-1">
                                        {{ $item->address }}
                                        @php
                                            $itemWardName = $getRelationName($item->ward);
                                            $itemDistrictName = $getRelationName($item->district);
                                            $itemProvinceName = $getRelationName($item->province);
                                        @endphp
                                        @if ($itemWardName || $itemDistrictName || $itemProvinceName)
                                            <span class="text-muted">, {{ $itemWardName }} {{ $itemDistrictName }} {{ $itemProvinceName }}</span>
                                        @endif
                                    </p>
                                    @if ($item->phone)
                                        <p class="professional-address-phone mb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                            </svg>
                                            {{ $item->phone }}
                                        </p>
                                    @endif
                                </div>
                                <div class="professional-address-actions">
                                    <x-ui::button color="primary" size="sm" icon="edit"
                                            wire:click="$dispatch('show-modal-create-branch-taking-address', { branch_id: {{ $id }}, id: {{ $item->id }} })">
                                        {{ __('core/base::general.edit') }}
                                    </x-ui::button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($addresses instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                        <div class="professional-pagination">
                            {{ $addresses->links() }}
                        </div>
                    @endif
                @endif
            </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="professional-detail-actions">
        @if ($row->status)
            <x-ui::button x-show="activeTab === 1" color="danger" size="sm" icon="player-pause" :outline="true"
                    wire:click="$dispatch('toggleActive', { id: {{ $id }}, status: 0 })">
                Tắt hoạt động
            </x-ui::button>
        @else
            <x-ui::button x-show="activeTab === 1" color="success" size="sm" icon="check" :outline="true"
                    wire:click="$dispatch('toggleActive', { id: {{ $id }}, status: 1 })">
                Bật hoạt động
            </x-ui::button>
        @endif

        <x-ui::button x-show="activeTab === 3" color="primary" icon="plus"
                wire:click="$dispatch('show-modal-create-branch-taking-address', { branch_id: {{ $id }} })">
            {{ __('core/base::general.add_address') }}
        </x-ui::button>
    </div>
</div>
