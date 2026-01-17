{{-- Context Menu --}}
<div class="media-context-menu"
     x-show="contextMenu"
     x-cloak
     :style="'left: ' + contextX + 'px; top: ' + contextY + 'px;'"
     @click.outside="contextMenu = false"
     @keydown.escape.window="contextMenu = false">

    {{-- File Menu --}}
    <template x-if="selectedType === 'file'">
        <div class="context-menu-content">
            {{-- View/Preview Group --}}
            <div class="context-menu-section">
                <div class="context-menu-item" @click="showSidebar = true; $wire.loadMediaDetails(selectedItem); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('info-circle', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.details') }}</span>
                </div>

                <div class="context-menu-item" @click="window.open(document.querySelector(`[data-media-id='${selectedItem}']`)?.dataset.url || '', '_blank'); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('eye', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.view_image') }}</span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            {{-- Edit Group --}}
            <div class="context-menu-section">
                <div class="context-menu-item" @click="$wire.openImageEditor(selectedItem); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('photo-edit', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.edit_image') }}</span>
                </div>

                <div class="context-menu-item" @click="$wire.openRenameModal(selectedItem, 'file'); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('edit', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.rename') }}</span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            {{-- Copy/Move Group --}}
            <div class="context-menu-section">
                <div class="context-menu-item" @click="navigator.clipboard.writeText(document.querySelector(`[data-media-id='${selectedItem}']`)?.dataset.url || ''); contextMenu = false; alert('{{ __('core/media::media.url_copied') }}')">
                    <span class="menu-icon">{!! tabler_icon('copy', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.copy_url') }}</span>
                </div>

                <div class="context-menu-item" @click="$wire.cut([selectedItem]); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('scissors', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.move') }}</span>
                </div>

                <div class="context-menu-item" @click="(() => { const a = document.createElement('a'); a.href = document.querySelector(`[data-media-id='${selectedItem}']`)?.dataset.url || ''; a.download = ''; a.click(); })(); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('download', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.download') }}</span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            {{-- Danger Zone --}}
            <div class="context-menu-section">
                <div class="context-menu-item context-menu-danger" @click="if(confirm('{{ __('core/media::media.confirm_delete_file') }}')) { $wire.deleteItem(selectedItem, 'file') }; contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('trash', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.delete') }}</span>
                </div>
            </div>
        </div>
    </template>

    {{-- Folder Menu --}}
    <template x-if="selectedType === 'folder'">
        <div class="context-menu-content">
            <div class="context-menu-section">
                <div class="context-menu-item" @click="$wire.navigateToFolder(selectedItem); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('folder-open', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.open') }}</span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            <div class="context-menu-section">
                <div class="context-menu-item" @click="$wire.openRenameModal(selectedItem, 'folder'); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('edit', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.rename') }}</span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            <div class="context-menu-section">
                <div class="context-menu-item context-menu-danger" @click="if(confirm('{{ __('core/media::media.confirm_delete_folder') }}')) { $wire.deleteItem(selectedItem, 'folder') }; contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('trash', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.delete') }}</span>
                </div>
            </div>
        </div>
    </template>
</div>
