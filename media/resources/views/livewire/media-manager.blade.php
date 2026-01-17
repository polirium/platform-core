@props(['search', 'filterType', 'viewMode', 'breadcrumbs', 'currentFolder', 'clipboard', 'selectedMedia', 'selectedMediaDetails', 'mediaItems', 'folders'])

<div x-data="{
    contextMenu: false,
    contextX: 0,
    contextY: 0,
    selectedItem: null,
    selectedType: null,
    showSidebar: false,
    sidebarItem: null,
    sidebarType: null
}"
@click="if(!$event.target.closest('.media-context-menu') && !$event.target.closest('.media-grid-item') && !$event.target.closest('.folder-item')) { contextMenu = false }"
@keydown.escape.window="contextMenu = false; showSidebar = false"
class="media-manager">

    {{-- Hidden Upload Input --}}
    <input type="file" id="media-upload-input" wire:model="files" multiple class="d-none"
           accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.txt">

    {{-- Main Layout Container --}}
    <div class="media-layout">
        {{-- Main Content Area --}}
        <div class="media-content" :class="showSidebar ? 'sidebar-open' : ''">

            {{-- Toolbar --}}
            @include('core/media::components.toolbar', [
                'search' => $search,
                'filterType' => $filterType,
                'viewMode' => $viewMode,
                'breadcrumbs' => $breadcrumbs,
                'currentFolder' => $currentFolder
            ])

            {{-- Clipboard Bar --}}
            @if(count($clipboard) > 0)
                <div class="media-clipboard-bar">
                    <span class="clipboard-info">
                        {!! tabler_icon('clipboard', ['class' => 'icon']) !!}
                        {{ count($clipboard) }} {{ __('core/media::media.files_waiting') }}
                    </span>
                    <div class="clipboard-actions">
                        <x-ui::button color="success" size="sm" icon="clipboard" wire:click="paste">
                            {{ __('core/media::media.paste_here') }}
                        </x-ui::button>
                        <x-ui::button color="secondary" size="sm" icon="x" :outline="true" wire:click="clearClipboard">
                            {{ __('core/media::media.cancel') }}
                        </x-ui::button>
                    </div>
                </div>
            @endif

            {{-- Selection Bar --}}
            @if(count($selectedMedia) > 0)
                <div class="media-selection-bar">
                    <span class="selection-info">
                        {!! tabler_icon('checkbox', ['class' => 'icon']) !!}
                        {{ count($selectedMedia) }} {{ __('core/media::media.items_selected') }}
                    </span>
                    <div class="selection-actions">
                        <x-ui::button color="secondary" size="sm" icon="scissors" :outline="true" wire:click="cut({{ json_encode($selectedMedia) }})">
                            {{ __('core/media::media.cut') }}
                        </x-ui::button>
                        <x-ui::button color="danger" size="sm" icon="trash" :outline="true" wire:click="deleteSelected" wire:confirm="{{ __('core/media::media.confirm_delete_selected') }}">
                            {{ __('core/media::media.delete') }}
                        </x-ui::button>
                        <x-ui::button color="secondary" size="sm" icon="x" :outline="true" wire:click="clearSelection">
                            {{ __('core/media::media.clear_selection') }}
                        </x-ui::button>
                    </div>
                </div>
            @endif

            {{-- Upload Progress --}}
            <div wire:loading wire:target="files" class="media-loading">
                <div class="spinner-border spinner-border-sm me-2"></div>
                {{ __('core/media::media.uploading') }}
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="media-alert media-alert-success">
                    {{ session('success') }}
                    <button type="button" class="media-alert-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="media-alert media-alert-danger">
                    {{ session('error') }}
                    <button type="button" class="media-alert-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="media-alert media-alert-info">
                    {{ session('info') }}
                    <button type="button" class="media-alert-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Content Area --}}
            @if($viewMode === 'grid')
                <div class="media-grid">
                    {{-- Back Button --}}
                    @if($currentFolder)
                        <div class="media-grid-item media-back-btn" wire:click="navigateUp">
                            <div class="media-thumbnail">
                                {!! tabler_icon('arrow-left', ['class' => 'icon-back']) !!}
                            </div>
                            <div class="media-info">
                                <div class="media-name">{{ __('core/media::media.back') }}</div>
                            </div>
                        </div>
                    @endif

                    {{-- Folders --}}
                    @foreach($folders as $folder)
                        <div class="media-grid-item folder-item"
                             wire:dblclick="navigateToFolder('{{ $folder['path'] }}')"
                             @click="showSidebar = true; sidebarItem = '{{ $folder['path'] }}'; sidebarType = 'folder'"
                             @contextmenu.prevent="contextMenu = true; contextX = $event.clientX; contextY = $event.clientY; selectedItem = '{{ $folder['path'] }}'; selectedType = 'folder'">
                            <div class="media-thumbnail">
                                {!! tabler_icon('folder', ['class' => 'icon-folder']) !!}
                            </div>
                            <div class="media-info">
                                <div class="media-name" title="{{ $folder['name'] }}">{{ Str::limit($folder['name'], 12) }}</div>
                                <div class="media-meta">{{ __('core/media::media.folder') }}</div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Files --}}
                    @forelse($mediaItems as $item)
                        <div class="media-grid-item {{ in_array($item->id, $selectedMedia) ? 'selected' : '' }}"
                             data-media-id="{{ $item->id }}"
                             data-url="{{ $item->getUrl() }}"
                             @click="showSidebar = true; sidebarType = 'file'; $wire.loadMediaDetails({{ $item->id }})"
                             @contextmenu.prevent="contextMenu = true; contextX = $event.clientX; contextY = $event.clientY; selectedItem = {{ $item->id }}; selectedType = 'file'">

                            <label class="media-checkbox" @click.stop>
                                <input type="checkbox" class="form-check-input"
                                       {{ in_array($item->id, $selectedMedia) ? 'checked' : '' }}
                                       wire:click="toggleSelect({{ $item->id }})">
                            </label>

                            <div class="media-thumbnail">
                                @if($item->is_image)
                                    <img src="{{ $item->getUrl() }}" alt="{{ $item->name }}" loading="lazy">
                                @elseif($item->is_video)
                                    {!! tabler_icon('video', ['class' => 'icon-media']) !!}
                                @elseif($item->is_document)
                                    {!! tabler_icon('file-text', ['class' => 'icon-media']) !!}
                                @else
                                    {!! tabler_icon('file', ['class' => 'icon-media']) !!}
                                @endif
                            </div>

                            <div class="media-info">
                                <div class="media-name" title="{{ $item->file_name }}">{{ Str::limit($item->name, 12) }}</div>
                                <div class="media-meta">{{ $item->formatted_size }}</div>
                            </div>
                        </div>
                    @empty
                        @if(count($folders) === 0 && !$currentFolder)
                            <div class="media-empty">
                                <div class="media-empty-icon">
                                    {!! tabler_icon('photo', ['class' => 'icon']) !!}
                                </div>
                                <p class="media-empty-title">{{ __('core/media::media.no_files') }}</p>
                                <p class="media-empty-subtitle">{{ __('core/media::media.no_files_hint') }}</p>
                            </div>
                        @endif
                    @endforelse
                </div>
            @else
                {{-- List View --}}
                <div class="media-table-container">
                    <table class="table table-hover table-vcenter media-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>{{ __('core/media::media.name') }}</th>
                                <th>{{ __('core/media::media.type') }}</th>
                                <th>{{ __('core/media::media.size') }}</th>
                                <th>{{ __('core/media::media.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($currentFolder)
                                <tr wire:click="navigateUp" class="cursor-pointer">
                                    <td></td>
                                    <td colspan="5" class="text-muted">
                                        {!! tabler_icon('arrow-left', ['class' => 'icon me-2']) !!}
                                        {{ __('core/media::media.back') }}
                                    </td>
                                </tr>
                            @endif

                            @foreach($folders as $folder)
                                <tr wire:dblclick="navigateToFolder('{{ $folder['path'] }}')"
                                    @contextmenu.prevent="contextMenu = true; contextX = $event.clientX; contextY = $event.clientY; selectedItem = '{{ $folder['path'] }}'; selectedType = 'folder'"
                                    class="cursor-pointer">
                                    <td></td>
                                    <td>
                                        {!! tabler_icon('folder', ['class' => 'icon-folder me-2']) !!}
                                        {{ $folder['name'] }}
                                    </td>
                                    <td class="text-muted">{{ __('core/media::media.folder') }}</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @endforeach

                            @forelse($mediaItems as $item)
                                <tr @click="$wire.loadMediaDetails({{ $item->id }}); showSidebar = true"
                                    @contextmenu.prevent="contextMenu = true; contextX = $event.clientX; contextY = $event.clientY; selectedItem = {{ $item->id }}; selectedType = 'file'"
                                    class="cursor-pointer {{ in_array($item->id, $selectedMedia) ? 'table-primary' : '' }}">
                                    <td @click.stop>
                                        <input type="checkbox" class="form-check-input"
                                               {{ in_array($item->id, $selectedMedia) ? 'checked' : '' }}
                                               wire:click="toggleSelect({{ $item->id }})">
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-muted">{{ $item->mime_type }}</td>
                                    <td class="text-muted">{{ $item->formatted_size }}</td>
                                    <td class="text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                @if(count($folders) === 0)
                                    <tr><td colspan="5" class="text-center text-muted py-4">{{ __('core/media::media.no_data') }}</td></tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Pagination --}}
            @if($mediaItems->hasPages())
                <div class="media-pagination">{{ $mediaItems->links() }}</div>
            @endif
        </div>

        {{-- Right Sidebar --}}
        @include('core/media::components.sidebar', ['selectedMedia' => $selectedMediaDetails])
    </div>

    {{-- Context Menu --}}
    @include('core/media::components.context-menu')

    {{-- Modals --}}
    @include('core/media::components.modals.create-folder')
    @include('core/media::components.modals.rename')
    @include('core/media::components.modals.image-editor')
</div>
