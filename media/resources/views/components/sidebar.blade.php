@props(['selectedMedia' => null, 'selectedFolder' => null])

{{-- Backdrop Overlay (Mobile & Desktop) --}}
<div x-show="showSidebar"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="media-sidebar-backdrop"
     @click="showSidebar = false"
     aria-hidden="true"></div>

{{-- Sidebar Panel --}}
<div class="media-sidebar"
     x-show="showSidebar"
     x-cloak
     x-transition:enter="transition transform ease-out duration-300"
     x-transition:enter-start="translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition transform ease-in duration-200"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="translate-x-full"
     @keydown.escape.window="showSidebar = false"
     role="dialog"
     aria-modal="true">
    {{-- Sidebar Header --}}
    <div class="media-sidebar-header">
        <h6 class="media-sidebar-title">{{ __('core/media::media.details') }}</h6>
        <button type="button" class="sidebar-close" @click="showSidebar = false">
            {!! tabler_icon('x', ['class' => 'icon']) !!}
        </button>
    </div>

    {{-- Loading Overlay --}}
    <div class="sidebar-loading-overlay" wire:loading.flex wire:target="loadMediaDetails, loadFolderDetails">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div class="sidebar-content">
        @if($selectedMedia)
            {{-- Preview Area --}}
            <div class="sidebar-preview-container">
                <div class="sidebar-preview-wrapper">
                    @if(str_starts_with($selectedMedia->mime_type, 'image/'))
                        <img src="{{ $selectedMedia->getSecureUrl() }}" class="sidebar-preview-img" alt="{{ $selectedMedia->name }}">
                    @elseif(str_starts_with($selectedMedia->mime_type, 'video/'))
                        <video src="{{ $selectedMedia->getSecureUrl() }}" class="sidebar-preview-video" controls></video>
                    @else
                        <div class="sidebar-preview-file">
                            {!! tabler_icon('file', ['class' => 'icon-xl']) !!}
                        </div>
                    @endif
                </div>
            </div>

            {{-- File Header --}}
            <div class="sidebar-header-info">
                <h4 class="file-name" title="{{ $selectedMedia->name }}">{{ $selectedMedia->name }}</h4>
                <div class="file-meta text-muted">
                    <span class="badge bg-secondary-lt">{{ $selectedMedia->mime_type }}</span>
                    <span class="mx-1">&bull;</span>
                    <span>{{ $selectedMedia->formatted_size }}</span>
                </div>
            </div>

            {{-- Properties List --}}
            <div class="sidebar-properties">
                <div class="property-item">
                    <span class="property-label">{{ __('core/media::media.file_name') }}</span>
                    <span class="property-value font-monospace" title="{{ $selectedMedia->file_name }}">{{ $selectedMedia->file_name }}</span>
                </div>

                @if(str_starts_with($selectedMedia->mime_type, 'image/') && $selectedMedia->custom_properties)
                <div class="property-item">
                    <span class="property-label">{{ __('core/media::media.dimensions') }}</span>
                    <span class="property-value">{{ $selectedMedia->custom_properties['width'] ?? '-' }} &times; {{ $selectedMedia->custom_properties['height'] ?? '-' }} px</span>
                </div>
                @endif

                <div class="property-item">
                    <span class="property-label">{{ __('core/media::media.uploaded_at') }}</span>
                    <span class="property-value">{{ $selectedMedia->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="property-item property-url">
                    <span class="property-label">{{ __('core/media::media.url') }}</span>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" value="{{ $selectedMedia->getSecureUrl() }}" readonly>
                        <button class="btn btn-icon" type="button" onclick="navigator.clipboard.writeText('{{ $selectedMedia->getSecureUrl() }}')" title="{{ __('core/media::media.copy_url') }}">
                            {!! tabler_icon('copy', ['class' => 'icon']) !!}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="sidebar-actions mt-auto">
                <div class="d-grid gap-2">
                    <div>
                        <a href="{{ $selectedMedia->getSecureUrl() }}" target="_blank" class="btn btn-outline-primary w-100" wire:key="action-open-{{ $selectedMedia->id }}">
                            {!! tabler_icon('external-link', ['class' => 'icon']) !!} {{ __('core/media::media.open_new_tab') }}
                        </a>
                    </div>

                    @if(str_starts_with($selectedMedia->mime_type, 'image/'))
                        <div>
                            <button type="button" class="btn btn-outline-secondary w-100" wire:click.prevent.stop="openImageEditor({{ $selectedMedia->id }})" wire:key="action-edit-{{ $selectedMedia->id }}">
                                {!! tabler_icon('photo-edit', ['class' => 'icon']) !!} {{ __('core/media::media.edit_image') }}
                            </button>
                        </div>
                    @endif

                    <div>
                        <button type="button" class="btn btn-outline-danger w-100" wire:click.prevent.stop="deleteItem({{ $selectedMedia->id }}, 'file')" wire:confirm="{{ __('core/media::media.confirm_delete') }}" wire:key="action-delete-{{ $selectedMedia->id }}">
                            {!! tabler_icon('trash', ['class' => 'icon']) !!} {{ __('core/media::media.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        @elseif($selectedFolder)
            {{-- Folder Info --}}
            <div class="sidebar-preview-container">
                <div class="sidebar-preview-wrapper">
                    <div class="sidebar-preview-file folder-preview">
                        {!! tabler_icon('folder', ['class' => 'icon-folder-lg']) !!}
                    </div>
                </div>
            </div>

            {{-- Folder Header --}}
            <div class="sidebar-header-info">
                <h4 class="file-name" title="{{ $selectedFolder['name'] }}">{{ $selectedFolder['name'] }}</h4>
                <div class="file-meta text-muted">
                    <span>{{ __('core/media::media.folder') }}</span>
                    <span class="mx-1">&bull;</span>
                    <span>{{ $selectedFolder['item_count'] }} {{ __('core/media::media.items') }}</span>
                </div>
            </div>

            {{-- Folder Properties --}}
            <div class="sidebar-properties">
                <div class="property-item">
                    <span class="property-label">{{ __('core/media::media.name') }}</span>
                    <span class="property-value font-monospace">{{ $selectedFolder['name'] }}</span>
                </div>

                <div class="property-item">
                    <span class="property-label">{{ __('core/media::media.path') }}</span>
                    <span class="property-value font-monospace">{{ $selectedFolder['path'] ?: '/' }}</span>
                </div>

                <div class="property-item">
                    <span class="property-label">{{ __('core/media::media.modified_at') }}</span>
                    <span class="property-value">{{ $selectedFolder['last_modified'] }}</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="sidebar-actions mt-auto">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary w-100" wire:click="navigateToFolder('{{ $selectedFolder['path'] }}')">
                        {!! tabler_icon('folder-open', ['class' => 'icon']) !!} {{ __('core/media::media.open') }}
                    </button>

                    <button type="button" class="btn btn-outline-secondary w-100" wire:click="openRenameModal('{{ $selectedFolder['path'] }}', 'folder')">
                        {!! tabler_icon('edit', ['class' => 'icon']) !!} {{ __('core/media::media.rename') }}
                    </button>

                    <button type="button" class="btn btn-outline-danger w-100" wire:click="deleteItem('{{ $selectedFolder['path'] }}', 'folder')" wire:confirm="{{ __('core/media::media.confirm_delete_folder') }}">
                        {!! tabler_icon('trash', ['class' => 'icon']) !!} {{ __('core/media::media.delete') }}
                    </button>
                </div>
            </div>

        @else
            <div class="sidebar-empty">
                <div class="empty-icon-wrapper">
                    {!! tabler_icon('photo', ['class' => 'icon-empty']) !!}
                </div>
                <p class="sidebar-empty-text text-muted">{{ __('core/media::media.select_file_details') }}</p>
            </div>
        @endif
    </div>
</div>
