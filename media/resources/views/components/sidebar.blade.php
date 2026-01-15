@props(['selectedMedia'])

{{-- Sidebar Panel --}}
<div class="media-sidebar p-3" x-show="showSidebar" x-cloak>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">{{ __('core/media::media.details') }}</h6>
        <button type="button" class="btn-close btn-close-sm" @click="showSidebar = false"></button>
    </div>

    @if($selectedMedia)
        {{-- Preview --}}
        <div class="sidebar-preview text-center mb-3">
            @if(str_starts_with($selectedMedia->mime_type, 'image/'))
                <img src="{{ $selectedMedia->getUrl() }}" class="img-fluid rounded" alt="">
            @elseif(str_starts_with($selectedMedia->mime_type, 'video/'))
                <video src="{{ $selectedMedia->getUrl() }}" class="img-fluid rounded" controls style="max-height: 200px;"></video>
            @else
                <div class="bg-light rounded p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="text-secondary"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <dl class="sidebar-info small">
            <dt>{{ __('core/media::media.name') }}</dt>
            <dd>{{ $selectedMedia->name }}</dd>

            <dt>{{ __('core/media::media.file_name') }}</dt>
            <dd>{{ $selectedMedia->file_name }}</dd>

            <dt>{{ __('core/media::media.type') }}</dt>
            <dd>{{ $selectedMedia->mime_type }}</dd>

            <dt>{{ __('core/media::media.size') }}</dt>
            <dd>{{ $selectedMedia->formatted_size }}</dd>

            @if(str_starts_with($selectedMedia->mime_type, 'image/') && $selectedMedia->custom_properties)
                <dt>{{ __('core/media::media.image_size') }}</dt>
                <dd>{{ $selectedMedia->custom_properties['width'] ?? '?' }} x {{ $selectedMedia->custom_properties['height'] ?? '?' }} px</dd>
            @endif

            <dt>{{ __('core/media::media.directory') }}</dt>
            <dd>{{ $selectedMedia->collection_name ?: 'uploads' }}</dd>

            <dt>{{ __('core/media::media.uploaded_at') }}</dt>
            <dd>{{ $selectedMedia->created_at->format('d/m/Y H:i') }}</dd>
        </dl>

        {{-- URL Copy --}}
        <div class="mb-3">
            <label class="form-label small">{{ __('core/media::media.url') }}</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" value="{{ $selectedMedia->getUrl() }}" readonly>
                <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ $selectedMedia->getUrl() }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-grid gap-2">
            <a href="{{ $selectedMedia->getUrl() }}" target="_blank" class="btn btn-sm btn-outline-primary">
                {{ __('core/media::media.open_new_tab') }}
            </a>
            @if(str_starts_with($selectedMedia->mime_type, 'image/'))
                <button type="button" wire:click="openImageEditor({{ $selectedMedia->id }})" class="btn btn-sm btn-outline-secondary">
                    {{ __('core/media::media.edit_image') }}
                </button>
            @endif
            <button type="button" wire:click="deleteMedia({{ $selectedMedia->id }})"
                    onclick="return confirm('{{ __('core/media::media.confirm_delete') }}')"
                    class="btn btn-sm btn-outline-danger">
                {{ __('core/media::media.delete') }}
            </button>
        </div>
    @else
        <p class="text-muted small">{{ __('core/media::media.select_file_details') }}</p>
    @endif
</div>
