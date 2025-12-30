@props(['search', 'filterType', 'viewMode', 'breadcrumbs', 'currentFolder'])

{{-- Toolbar --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        {{-- Breadcrumbs --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="#" wire:click.prevent="goToRoot" class="text-decoration-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    </a>
                </li>
                @foreach($breadcrumbs as $crumb)
                    <li class="breadcrumb-item">
                        <a href="#" wire:click.prevent="navigateToFolder('{{ $crumb['path'] }}')" class="text-decoration-none">
                            {{ $crumb['name'] }}
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>

    <div class="d-flex gap-2 flex-wrap align-items-center">
        {{-- Search --}}
        <div class="input-icon" style="width: 180px;">
            <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm" placeholder="{{ __('Tìm kiếm...') }}">
        </div>

        {{-- Filter --}}
        <select wire:model.live="filterType" class="form-select form-select-sm" style="width: auto;">
            <option value="">{{ __('Tất cả') }}</option>
            <option value="image">{{ __('Hình ảnh') }}</option>
            <option value="video">{{ __('Video') }}</option>
            <option value="document">{{ __('Tài liệu') }}</option>
            <option value="audio">{{ __('Âm thanh') }}</option>
        </select>

        {{-- New Folder Button --}}
        <button type="button" wire:click="openCreateFolderModal" class="btn btn-sm btn-outline-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 10v6"/><path d="M9 13h6"/><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
            {{ __('Tạo Folder') }}
        </button>

        {{-- Upload Button --}}
        <button type="button" onclick="document.getElementById('media-upload-input').click();" class="btn btn-sm btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            {{ __('Upload') }}
        </button>

        {{-- View Mode Toggle --}}
        <div class="btn-group view-mode-toggle" role="group" aria-label="View mode">
            <button type="button" wire:click="setViewMode('grid')" class="btn btn-icon {{ $viewMode === 'grid' ? 'btn-primary' : 'btn-outline-secondary' }}" style="padding: 0.375rem 0.5rem; border-radius: 4px 0 0 4px;" title="{{ __('Lưới') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </button>
            <button type="button" wire:click="setViewMode('list')" class="btn btn-icon {{ $viewMode === 'list' ? 'btn-primary' : 'btn-outline-secondary' }}" style="padding: 0.375rem 0.5rem; border-radius: 0 4px 4px 0; margin-left: -1px;" title="{{ __('Danh sách') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </button>
        </div>
    </div>
</div>
