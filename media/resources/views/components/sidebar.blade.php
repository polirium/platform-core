@props(['selectedMedia'])

{{-- Sidebar Panel --}}
<div class="media-sidebar" x-show="showSidebar" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4">
    {{-- Sidebar Header --}}
    <div class="media-sidebar-header">
        <h6 class="media-sidebar-title">{{ __('core/media::media.details') }}</h6>
        <button type="button" class="sidebar-close" @click="showSidebar = false">
            {!! tabler_icon('x', ['class' => 'icon']) !!}
        </button>
    </div>

    @if($selectedMedia)
        {{-- Preview --}}
        <div class="sidebar-preview">
            @if(str_starts_with($selectedMedia->mime_type, 'image/'))
                <img src="{{ $selectedMedia->getUrl() }}" class="sidebar-preview-img" alt="{{ $selectedMedia->name }}">
            @elseif(str_starts_with($selectedMedia->mime_type, 'video/'))
                <video src="{{ $selectedMedia->getUrl() }}" class="sidebar-preview-video" controls></video>
            @else
                <div class="sidebar-preview-file">
                    {!! tabler_icon('file', ['class' => 'icon-file']) !!}
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="sidebar-info">
            <div class="info-row">
                <span class="info-label">{{ __('core/media::media.name') }}</span>
                <span class="info-value">{{ $selectedMedia->name }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">{{ __('core/media::media.file_name') }}</span>
                <span class="info-value info-value-mono">{{ $selectedMedia->file_name }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">{{ __('core/media::media.type') }}</span>
                <span class="info-value">{{ $selectedMedia->mime_type }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">{{ __('core/media::media.size') }}</span>
                <span class="info-value">{{ $selectedMedia->formatted_size }}</span>
            </div>

            @if(str_starts_with($selectedMedia->mime_type, 'image/') && $selectedMedia->custom_properties)
                <div class="info-row">
                    <span class="info-label">{{ __('core/media::media.image_size') }}</span>
                    <span class="info-value">{{ $selectedMedia->custom_properties['width'] ?? '?' }} x {{ $selectedMedia->custom_properties['height'] ?? '?' }} px</span>
                </div>
            @endif

            <div class="info-row">
                <span class="info-label">{{ __('core/media::media.directory') }}</span>
                <span class="info-value">{{ $selectedMedia->collection_name ?: 'uploads' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">{{ __('core/media::media.uploaded_at') }}</span>
                <span class="info-value">{{ $selectedMedia->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        {{-- URL Copy --}}
        <div class="sidebar-url-copy">
            <label class="sidebar-label">{{ __('core/media::media.url') }}</label>
            <div class="url-copy-group">
                <input type="text" class="form-control form-control-sm url-input" value="{{ $selectedMedia->getUrl() }}" readonly>
                <button type="button" class="btn-copy-url" onclick="navigator.clipboard.writeText('{{ $selectedMedia->getUrl() }}')" title="{{ __('core/media::media.copy_url') }}">
                    {!! tabler_icon('copy', ['class' => 'icon']) !!}
                </button>
            </div>
        </div>

        {{-- Actions --}}
        <div class="sidebar-actions">
            <x-ui::button color="secondary" size="sm" icon="external-link" :outline="true" href="{{ $selectedMedia->getUrl() }}" target="_blank" class="w-100">
                {{ __('core/media::media.open_new_tab') }}
            </x-ui::button>

            @if(str_starts_with($selectedMedia->mime_type, 'image/'))
                <x-ui::button color="secondary" size="sm" icon="photo-edit" :outline="true" wire:click="openImageEditor({{ $selectedMedia->id }})" class="w-100">
                    {{ __('core/media::media.edit_image') }}
                </x-ui::button>
            @endif

            <x-ui::button color="danger" size="sm" icon="trash" :outline="true" wire:click="deleteItem({{ $selectedMedia->id }}, 'file')" wire:confirm="{{ __('core/media::media.confirm_delete') }}" class="w-100">
                {{ __('core/media::media.delete') }}
            </x-ui::button>
        </div>
    @else
        <div class="sidebar-empty">
            {!! tabler_icon('photo', ['class' => 'icon-empty']) !!}
            <p class="sidebar-empty-text">{{ __('core/media::media.select_file_details') }}</p>
        </div>
    @endif
</div>
