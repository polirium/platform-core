@props(['media', 'selected' => []])

<div class="media-grid-item {{ in_array($media->id, $selected) ? 'selected' : '' }}"
     wire:click="toggleSelect({{ $media->id }})"
     @contextmenu.prevent="$dispatch('open-context-menu', { event: $event, id: {{ $media->id }}, type: 'file' })">

    {{-- Checkbox --}}
    <div class="media-checkbox">
        <input type="checkbox" class="form-check-input"
               {{ in_array($media->id, $selected) ? 'checked' : '' }}
               @click.stop>
    </div>

    {{-- Thumbnail --}}
    <div class="media-thumbnail">
        @if(str_starts_with($media->mime_type, 'image/'))
            <img src="{{ $media->getSecureUrl() }}" alt="{{ $media->name }}" loading="lazy">
        @elseif(str_starts_with($media->mime_type, 'video/'))
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="text-primary"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><line x1="7" y1="2" x2="7" y2="22"/><line x1="17" y1="2" x2="17" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="2" y1="7" x2="7" y2="7"/><line x1="2" y1="17" x2="7" y2="17"/><line x1="17" y1="17" x2="22" y2="17"/><line x1="17" y1="7" x2="22" y2="7"/></svg>
        @elseif(str_starts_with($media->mime_type, 'audio/'))
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="text-success"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="text-secondary"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        @endif
    </div>

    {{-- Info --}}
    <div class="media-info">
        <div class="media-name" title="{{ $media->name }}">{{ $media->name }}</div>
        <div class="media-meta">{{ $media->formatted_size }}</div>
    </div>
</div>
