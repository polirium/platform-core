@props(['search', 'filterType', 'viewMode', 'breadcrumbs', 'currentFolder'])

<style>
    @media (max-width: 768px) {
        .media-manager-toolbar { display: none !important; }
        .d-none.d-md-block { display: none !important; }
    }
</style>

{{-- Mobile Breadcrumb (separate, top) --}}
<div class="media-mobile-breadcrumb d-md-none">
    <div class="mobile-crumb">
        <button type="button"
                wire:click.prevent="goToRoot"
                class="breadcrumb-btn {{ $currentFolder === '' || $currentFolder === 'uploads' ? 'disabled' : '' }}"
                {{ $currentFolder === '' || $currentFolder === 'uploads' ? 'disabled' : '' }}>
            {!! tabler_icon('home', ['class' => 'icon icon-sm']) !!}
        </button>
    </div>
    @foreach($breadcrumbs as $crumb)
        <div class="mobile-crumb">
            <button type="button" wire:click.prevent="navigateToFolder('{{ $crumb['path'] }}')" class="breadcrumb-btn">
                {{ $crumb['name'] }}
            </button>
        </div>
    @endforeach
</div>

{{-- Desktop Toolbar --}}
<div class="d-none d-md-block">
    <div class="media-manager-toolbar" style="display: flex;">
        {{-- Left: Breadcrumbs --}}
        <div class="toolbar-left">
            <nav class="toolbar-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-sm mb-0">
                    <li class="breadcrumb-item">
                        <button type="button"
                                wire:click.prevent="goToRoot"
                                class="breadcrumb-btn breadcrumb-home {{ $currentFolder === '' || $currentFolder === 'uploads' ? 'disabled' : '' }}"
                                {{ $currentFolder === '' || $currentFolder === 'uploads' ? 'disabled' : '' }}>
                            {!! tabler_icon('home', ['class' => 'icon icon-sm']) !!}
                        </button>
                    </li>
                    @foreach($breadcrumbs as $crumb)
                        <li class="breadcrumb-item">
                            <button type="button" wire:click.prevent="navigateToFolder('{{ $crumb['path'] }}')" class="breadcrumb-btn">
                                {{ $crumb['name'] }}
                            </button>
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>

        {{-- Right: Actions --}}
        <div class="toolbar-right">
            {{-- Search --}}
            <div class="toolbar-search">
                <div class="input-icon input-icon-sm">
                    <span class="input-icon-addon">
                        {!! tabler_icon('search', ['class' => 'icon icon-sm']) !!}
                    </span>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           class="form-control form-control-sm"
                           placeholder="{{ __('core/media::media.search') }}">
                </div>
            </div>

            {{-- Filter --}}
            <div class="toolbar-filter">
                <select wire:model.live="filterType" class="form-select form-select-sm">
                    <option value="">{{ __('core/media::media.all') }}</option>
                    <option value="image">{{ __('core/media::media.image') }}</option>
                    <option value="video">{{ __('core/media::media.video') }}</option>
                    <option value="document">{{ __('core/media::media.document') }}</option>
                    <option value="audio">{{ __('core/media::media.audio') }}</option>
                </select>
            </div>

            {{-- Divider --}}
            <div class="toolbar-divider"></div>

            {{-- Actions --}}
            <div class="toolbar-actions">
                {{-- New Folder --}}
                @can('media.upload')
                <x-ui::button
                    color="secondary"
                    icon="folder-plus"
                    wire:click="createDefaultFolder"
                    :outline="true">
                    {{ __('core/media::media.create_folder') }}
                </x-ui::button>
                @endcan

                {{-- Upload --}}
                @can('media.upload')
                <x-ui::button
                    color="primary"
                    icon="upload"
                    onclick="document.getElementById('media-upload-input').click()">
                    {{ __('core/media::media.upload') }}
                </x-ui::button>
                @endcan
            </div>

            {{-- View Mode Toggle --}}
            <div class="toolbar-viewmode">
                <button type="button"
                        wire:click="setViewMode('grid')"
                        class="viewmode-btn {{ $viewMode === 'grid' ? 'active' : '' }}"
                        title="{{ __('core/media::media.grid') }}">
                    {!! tabler_icon('layout-grid', ['class' => 'icon']) !!}
                </button>
                <button type="button"
                        wire:click="setViewMode('list')"
                        class="viewmode-btn {{ $viewMode === 'list' ? 'active' : '' }}"
                        title="{{ __('core/media::media.list') }}">
                    {!! tabler_icon('list', ['class' => 'icon']) !!}
                </button>
            </div>

            {{-- Divider --}}
            <div class="toolbar-divider"></div>

            {{-- Trash Toggle --}}
            <div class="toolbar-trash">
                <button type="button"
                        wire:click="toggleTrash"
                        class="viewmode-btn {{ $this->showTrash ? 'active trash-active' : '' }}"
                        title="{{ $this->showTrash ? 'Quay lại' : 'Thùng rác' }}">
                    @if($this->showTrash)
                        {!! tabler_icon('arrow-left', ['class' => 'icon']) !!}
                    @else
                        {!! tabler_icon('trash', ['class' => 'icon']) !!}
                    @endif
                </button>
                @if($this->showTrash)
                    <button type="button"
                            wire:click="emptyTrash"
                            wire:confirm="Xác nhận xóa vĩnh viễn TẤT CẢ file trong thùng rác?"
                            class="btn btn-danger ms-2"
                            title="Dọn sạch thùng rác">
                        {!! tabler_icon('trash-x', ['class' => 'icon']) !!}
                        <span class="d-none d-md-inline">Dọn sạch</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Mobile Bottom Toolbar --}}
<div class="d-md-none">
    <div class="media-mobile-toolbar w-100 d-flex justify-content-between">
        {{-- Left: Folder & Upload --}}
        <div class="d-flex gap-1">
            {{-- New Folder --}}
            @can('media.upload')
            <button type="button"
                    wire:click="createDefaultFolder"
                    class="viewmode-btn"
                    title="{{ __('core/media::media.create_folder') }}">
                {!! tabler_icon('folder-plus', ['class' => 'icon']) !!}
            </button>
            @endcan

            {{-- Upload --}}
            @can('media.upload')
            <button type="button"
                    onclick="document.getElementById('media-upload-input').click()"
                    class="viewmode-btn text-primary"
                    title="{{ __('core/media::media.upload') }}">
                {!! tabler_icon('upload', ['class' => 'icon']) !!}
            </button>
            @endcan
        </div>

        {{-- Right: View Mode & Trash --}}
        <div class="d-flex gap-1">
            {{-- View Mode Toggle --}}
            <button type="button"
                    wire:click="setViewMode('grid')"
                    class="viewmode-btn {{ $viewMode === 'grid' ? 'active' : '' }}"
                    title="{{ __('core/media::media.grid') }}">
                {!! tabler_icon('layout-grid', ['class' => 'icon']) !!}
            </button>
            <button type="button"
                    wire:click="setViewMode('list')"
                    class="viewmode-btn {{ $viewMode === 'list' ? 'active' : '' }}"
                    title="{{ __('core/media::media.list') }}">
                {!! tabler_icon('list', ['class' => 'icon']) !!}
            </button>

            {{-- Trash Toggle --}}
            <button type="button"
                    wire:click="toggleTrash"
                    class="viewmode-btn {{ $this->showTrash ? 'active trash-active' : '' }}"
                    title="{{ $this->showTrash ? 'Quay lại' : 'Thùng rác' }}">
                @if($this->showTrash)
                    {!! tabler_icon('arrow-left', ['class' => 'icon']) !!}
                @else
                    {!! tabler_icon('trash', ['class' => 'icon']) !!}
                @endif
            </button>
        </div>
    </div>
</div>
