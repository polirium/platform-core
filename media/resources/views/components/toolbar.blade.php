@props(['search', 'filterType', 'viewMode', 'breadcrumbs', 'currentFolder'])

{{-- Media Manager Toolbar --}}
<div class="media-manager-toolbar">
    {{-- Left: Breadcrumbs --}}
    <div class="toolbar-left">
        <nav class="toolbar-breadcrumb" aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-sm mb-0">
                <li class="breadcrumb-item">
                    <button type="button" wire:click.prevent="goToRoot" class="breadcrumb-btn breadcrumb-home">
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
            <x-ui::button
                color="secondary"
                size="sm"
                icon="folder-plus"
                wire:click="openCreateFolderModal"
                :outline="true">
                {{ __('core/media::media.create_folder') }}
            </x-ui::button>

            {{-- Upload --}}
            <x-ui::button
                color="primary"
                size="sm"
                icon="upload"
                onclick="document.getElementById('media-upload-input').click()">
                {{ __('core/media::media.upload') }}
            </x-ui::button>
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
    </div>
</div>
