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
@click="if(!$event.target.closest('.context-menu') && !$event.target.closest('.media-item') && !$event.target.closest('.folder-item')) { contextMenu = false }"
@keydown.escape.window="contextMenu = false; showSidebar = false"
class="media-manager">

    {{-- Hidden Upload Input --}}
    <input type="file" id="media-upload-input" wire:model="files" multiple class="d-none"
           accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.txt">

    {{-- Main Container with Sidebar --}}
    <div class="d-flex">
        {{-- Left Content --}}
        <div class="flex-grow-1" :class="showSidebar ? 'me-3' : ''">

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
                <div class="alert alert-warning d-flex justify-content-between align-items-center py-2 mb-3">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>
                        {{ count($clipboard) }} {{ __('core/media::media.files_waiting') }}
                    </span>
                    <div class="d-flex gap-2">
                        <button type="button" wire:click="paste" class="btn btn-sm btn-success">
                            {{ __('core/media::media.paste_here') }}
                        </button>
                        <button type="button" wire:click="clearClipboard" class="btn btn-sm btn-outline-secondary">
                            {{ __('core/media::media.cancel') }}
                        </button>
                    </div>
                </div>
            @endif

            {{-- Selection Bar --}}
            @if(count($selectedMedia) > 0)
                <div class="alert alert-primary d-flex justify-content-between align-items-center py-2 mb-3">
                    <span>{{ count($selectedMedia) }} {{ __('core/media::media.items_selected') }}</span>
                    <div class="d-flex gap-2">
                        <button type="button" wire:click="cut({{ json_encode($selectedMedia) }})" class="btn btn-sm btn-outline-primary">
                            {{ __('core/media::media.cut') }}
                        </button>
                        <button type="button" wire:click="deleteSelected" onclick="return confirm('{{ __('core/media::media.confirm_delete_selected') }}')" class="btn btn-sm btn-outline-danger">
                            {{ __('core/media::media.delete') }}
                        </button>
                        <button type="button" wire:click="clearSelection" class="btn btn-sm btn-outline-secondary">
                            {{ __('core/media::media.clear_selection') }}
                        </button>
                    </div>
                </div>
            @endif

            {{-- Upload Progress --}}
            <div wire:loading wire:target="files" class="alert alert-info py-2 mb-3">
                <div class="spinner-border spinner-border-sm me-2"></div>
                {{ __('core/media::media.uploading') }}
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible py-2 mb-3">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible py-2 mb-3">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible py-2 mb-3">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Content Area --}}
            @if($viewMode === 'grid')
                <div class="media-grid">
                    {{-- Back Button --}}
                    @if($currentFolder)
                        <div class="media-grid-item" wire:click="navigateUp">
                            <div class="media-thumbnail">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1.5"><polyline points="15 18 9 12 15 6"/></svg>
                            </div>
                            <div class="media-info"><div class="media-name">{{ __('core/media::media.back') }}</div></div>
                        </div>
                    @endif

                    {{-- Folders --}}
                    @foreach($folders as $folder)
                        <div class="folder-item"
                             wire:dblclick="navigateToFolder('{{ $folder['path'] }}')"
                             @click="showSidebar = true; sidebarItem = '{{ $folder['path'] }}'; sidebarType = 'folder'"
                             @contextmenu.prevent="contextMenu = true; contextX = $event.clientX; contextY = $event.clientY; selectedItem = '{{ $folder['path'] }}'; selectedType = 'folder'">
                            <div class="media-thumbnail">
                                <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="#ffc107" stroke="#e6a800" stroke-width="0.5">
                                    <path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/>
                                </svg>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1.5"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                                @elseif($item->is_document)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                @endif
                            </div>

                            <div class="media-info">
                                <div class="media-name" title="{{ $item->file_name }}">{{ Str::limit($item->name, 12) }}</div>
                                <div class="media-meta">{{ $item->formatted_size }}</div>
                            </div>
                        </div>
                    @empty
                        @if(count($folders) === 0 && !$currentFolder)
                            <div class="col-12 w-100">
                                <div class="empty py-5 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="1.5" class="mb-3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <p class="empty-title text-muted">{{ __('core/media::media.no_files') }}</p>
                                    <p class="empty-subtitle text-muted small">{{ __('core/media::media.no_files_hint') }}</p>
                                </div>
                            </div>
                        @endif
                    @endforelse
                </div>
            @else
                {{-- List View --}}
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter">
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
                                    <td colspan="4" class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><polyline points="15 18 9 12 15 6"/></svg>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#ffc107" stroke="#e6a800" class="me-2"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
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
                <div class="mt-3">{{ $mediaItems->links() }}</div>
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

{{-- Load CSS/JS from built assets --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/polirium/core/media/css/media-manager.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('vendor/polirium/core/media/js/media-manager.js') }}"></script>
@endpush

