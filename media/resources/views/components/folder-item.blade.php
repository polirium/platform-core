@props(['folder', 'selected' => []])

<div class="folder-item"
     wire:click="navigateToFolder('{{ $folder['path'] }}')"
     @contextmenu.prevent="$dispatch('open-context-menu', { event: $event, id: '{{ $folder['path'] }}', type: 'folder' })">

    {{-- Folder Icon --}}
    <div class="media-thumbnail">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="#ffc107" stroke="#ffc107" stroke-width="1" class="folder-icon">
            <path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/>
        </svg>
    </div>

    {{-- Info --}}
    <div class="media-info">
        <div class="media-name" title="{{ $folder['name'] }}">{{ $folder['name'] }}</div>
        <div class="media-meta">Folder</div>
    </div>
</div>
