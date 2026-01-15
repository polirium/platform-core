{{-- Context Menu --}}
<div class="context-menu"
     x-show="contextMenu"
     x-cloak
     :style="'left: ' + contextX + 'px; top: ' + contextY + 'px;'"
     @click.outside="contextMenu = false">

    {{-- File Menu --}}
    <template x-if="selectedType === 'file'">
        <div>
            {{-- View/Preview Group --}}
            <div class="context-menu-item" @click="showSidebar = true; $wire.loadMediaDetails(selectedItem); contextMenu = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                {{ __('core/media::media.details') }}
            </div>

            <div class="context-menu-item" @click="window.open(document.querySelector(`[data-media-id='${selectedItem}']`)?.dataset.url || '', '_blank'); contextMenu = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                {{ __('core/media::media.view_image') }}
            </div>

            <div class="context-menu-divider"></div>

            {{-- Edit Group (for images) --}}
            <div class="context-menu-item" @click="$wire.openImageEditor(selectedItem); contextMenu = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                {{ __('core/media::media.edit_image') }}
            </div>

            <div class="context-menu-item" @click="$wire.openRenameModal(selectedItem, 'file'); contextMenu = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                {{ __('core/media::media.rename') }}
            </div>

            <div class="context-menu-divider"></div>

            {{-- Copy/Move Group --}}
            <div class="context-menu-item" @click="navigator.clipboard.writeText(document.querySelector(`[data-media-id='${selectedItem}']`)?.dataset.url || ''); contextMenu = false; alert('{{ __('core/media::media.url_copied') }}')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                {{ __('core/media::media.copy_url') }}
            </div>

            <div class="context-menu-item" @click="$wire.cut([selectedItem]); contextMenu = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
                {{ __('core/media::media.move') }}
            </div>

            {{-- Download --}}
            <div class="context-menu-item" @click="(() => { const a = document.createElement('a'); a.href = document.querySelector(`[data-media-id='${selectedItem}']`)?.dataset.url || ''; a.download = ''; a.click(); })(); contextMenu = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                {{ __('core/media::media.download') }}
            </div>

            <div class="context-menu-divider"></div>

            {{-- Danger Zone --}}
            <div class="context-menu-item text-danger" @click="if(confirm('{{ __('core/media::media.confirm_delete_file') }}')) { $wire.deleteMedia(selectedItem) }; contextMenu = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                {{ __('core/media::media.delete') }}
            </div>
        </div>
    </template>

    {{-- Folder Menu --}}
    <template x-if="selectedType === 'folder'">
        <div>
            <div class="context-menu-item" @click="$wire.navigateToFolder(selectedItem); contextMenu = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
                {{ __('core/media::media.open') }}
            </div>

            <div class="context-menu-divider"></div>

            <div class="context-menu-item" @click="$wire.openRenameModal(selectedItem, 'folder'); contextMenu = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                {{ __('core/media::media.rename') }}
            </div>

            <div class="context-menu-divider"></div>

            <div class="context-menu-item text-danger" @click="if(confirm('{{ __('core/media::media.confirm_delete_folder') }}')) { $wire.deleteFolder(selectedItem) }; contextMenu = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                {{ __('core/media::media.delete') }}
            </div>
        </div>
    </template>
</div>
