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

                <template x-if="selectedIsImage">
                    <div class="context-menu-item" @click="$wire.openPreviewModal(selectedItem); contextMenu = false">
                        <span class="menu-icon">{!! tabler_icon('eye', ['class' => 'icon']) !!}</span>
                        <span class="menu-text">{{ __('core/media::media.view_image') }}</span>
                    </div>
                </template>
            </div>

            <div class="context-menu-divider"></div>

            {{-- Edit Group --}}
            <div class="context-menu-section">
                <template x-if="selectedIsImage">
                    <div class="context-menu-item" @click="$wire.openImageEditor(selectedItem); contextMenu = false">
                        <span class="menu-icon">{!! tabler_icon('photo-edit', ['class' => 'icon']) !!}</span>
                        <span class="menu-text">{{ __('core/media::media.edit_image') }}</span>
                    </div>
                </template>

                <div class="context-menu-item" @click="renamingId = selectedItem; renamingType = 'file'; contextMenu = false; setTimeout(() => { let input = document.querySelector('input[data-rename-input=\'true\']:not([style*=\'display: none\'])'); if(input) { input.focus(); input.select(); } }, 100)">
                    <span class="menu-icon">{!! tabler_icon('edit', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.rename') }}</span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            {{-- Cut/Copy/Move Group --}}
            <div class="context-menu-section">
                <div class="context-menu-item" @click="navigator.clipboard.writeText(document.querySelector(`[data-media-id='${selectedItem}']`)?.dataset.url || ''); contextMenu = false; alert('{{ __('core/media::media.url_copied') }}')">
                    <span class="menu-icon">{!! tabler_icon('copy', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.copy_url') }}</span>
                </div>

                {{-- Cut (single or selected) --}}
                <div class="context-menu-item" @click="
                    let ids = selectedMediaIds.length > 0 && selectedMediaIds.includes(selectedItem) ? selectedMediaIds : [selectedItem];
                    $wire.cut(ids);
                    contextMenu = false
                ">
                    <span class="menu-icon">{!! tabler_icon('scissors', ['class' => 'icon']) !!}</span>
                    <span class="menu-text" x-text="selectedMediaIds.length > 1 && selectedMediaIds.includes(selectedItem) ? 'Cắt ' + selectedMediaIds.length + ' mục' : '{{ __('core/media::media.cut') }}'"></span>
                </div>

                <div class="context-menu-item" @click="(() => { const a = document.createElement('a'); a.href = document.querySelector(`[data-media-id='${selectedItem}']`)?.dataset.url || ''; a.download = ''; a.click(); })(); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('download', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.download') }}</span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            {{-- Danger Zone --}}
            <div class="context-menu-section">
                <div class="context-menu-item context-menu-danger" @click="
                    let ids = selectedMediaIds.length > 0 && selectedMediaIds.includes(selectedItem) ? selectedMediaIds : [selectedItem];
                    if(confirm(ids.length > 1 ? 'Xóa ' + ids.length + ' mục?' : '{{ __('core/media::media.confirm_delete_file') }}')) {
                        if(ids.length > 1) { $wire.deleteSelected() } else { $wire.deleteItem(selectedItem, 'file') }
                    };
                    contextMenu = false
                ">
                    <span class="menu-icon">{!! tabler_icon('trash', ['class' => 'icon']) !!}</span>
                    <span class="menu-text" x-text="selectedMediaIds.length > 1 && selectedMediaIds.includes(selectedItem) ? 'Xóa ' + selectedMediaIds.length + ' mục' : '{{ __('core/media::media.delete') }}'"></span>
                </div>
            </div>
        </div>
    </template>

    {{-- Folder Menu --}}
    <template x-if="selectedType === 'folder'">
        <div class="context-menu-content">
            <div class="context-menu-section">
                <div class="context-menu-item" @click="$wire.navigateToFolder(selectedItem.replace('folder:', '')); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('folder-open', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.open') }}</span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            <div class="context-menu-section">
                <div class="context-menu-item" @click="renamingId = selectedItem.replace('folder:', ''); renamingType = 'folder'; contextMenu = false; setTimeout(() => { let input = document.querySelector('input[data-rename-input=\'true\']:not([style*=\'display: none\'])'); if(input) { input.focus(); input.select(); } }, 100)">
                    <span class="menu-icon">{!! tabler_icon('edit', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.rename') }}</span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            <div class="context-menu-section">
                <div class="context-menu-item context-menu-danger" @click="if(confirm('{{ __('core/media::media.confirm_delete_folder') }}')) { $wire.deleteItem(selectedItem.replace('folder:', ''), 'folder') }; contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('trash', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.delete') }}</span>
                </div>
            </div>
        </div>
    </template>

    {{-- Background Menu (right-click on empty space) --}}
    <template x-if="selectedType === 'background'">
        <div class="context-menu-content">
            <div class="context-menu-section">
                <div class="context-menu-item" @click="$wire.set('showCreateFolderModal', true); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('folder-plus', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.create_folder') }}</span>
                </div>

                <div class="context-menu-item" @click="document.getElementById('media-upload-input')?.click(); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('upload', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.upload') }}</span>
                </div>
            </div>

            @if(count($clipboard ?? []) > 0)
            <div class="context-menu-divider"></div>
            <div class="context-menu-section">
                <div class="context-menu-item" @click="$wire.paste(); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('clipboard', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.paste_here') }} ({{ count($clipboard) }})</span>
                </div>
            </div>
            @endif

            <div class="context-menu-divider"></div>
            <div class="context-menu-section">
                <div class="context-menu-item" @click="$wire.$refresh(); contextMenu = false">
                    <span class="menu-icon">{!! tabler_icon('refresh', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">{{ __('core/media::media.refresh') }}</span>
                </div>
            </div>
        </div>
    </template>

    {{-- Trashed Item Menu (for files in trash view) --}}
    <template x-if="selectedType === 'trashed'">
        <div class="context-menu-content">
            {{-- Restore Group --}}
            <div class="context-menu-section">
                <div class="context-menu-item context-menu-success" @click="
                    let ids = selectedMediaIds.length > 0 && selectedMediaIds.includes(selectedItem) ? selectedMediaIds : [selectedItem];
                    if(ids.length > 1) { $wire.restoreSelected() } else { $wire.restoreItem(selectedItem) };
                    contextMenu = false
                ">
                    <span class="menu-icon">{!! tabler_icon('restore', ['class' => 'icon']) !!}</span>
                    <span class="menu-text" x-text="selectedMediaIds.length > 1 && selectedMediaIds.includes(selectedItem) ? 'Khôi phục ' + selectedMediaIds.length + ' mục' : 'Khôi phục'"></span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            {{-- Permanent Delete --}}
            <div class="context-menu-section">
                <div class="context-menu-item context-menu-danger" @click="
                    let ids = selectedMediaIds.length > 0 && selectedMediaIds.includes(selectedItem) ? selectedMediaIds : [selectedItem];
                    if(confirm(ids.length > 1 ? 'Xóa vĩnh viễn ' + ids.length + ' mục? Không thể khôi phục!' : 'Xóa vĩnh viễn file này? Không thể khôi phục!')) {
                        if(ids.length > 1) { $wire.forceDeleteSelected() } else { $wire.forceDeleteItem(selectedItem) }
                    };
                    contextMenu = false
                ">
                    <span class="menu-icon">{!! tabler_icon('trash-x', ['class' => 'icon']) !!}</span>
                    <span class="menu-text" x-text="selectedMediaIds.length > 1 && selectedMediaIds.includes(selectedItem) ? 'Xóa vĩnh viễn ' + selectedMediaIds.length + ' mục' : 'Xóa vĩnh viễn'"></span>
                </div>
            </div>
        </div>
    </template>

    {{-- Trashed Folder Menu --}}
    <template x-if="selectedType === 'trashed-folder'">
        <div class="context-menu-content">
            {{-- Restore Folder --}}
            <div class="context-menu-section">
                <div class="context-menu-item context-menu-success" @click="
                    $wire.restoreItem(selectedItem, 'folder');
                    contextMenu = false
                ">
                    <span class="menu-icon">{!! tabler_icon('restore', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">Khôi phục folder</span>
                </div>
            </div>

            <div class="context-menu-divider"></div>

            {{-- Permanent Delete Folder --}}
            <div class="context-menu-section">
                <div class="context-menu-item context-menu-danger" @click="
                    if(confirm('Xóa vĩnh viễn folder này và tất cả nội dung bên trong? Không thể khôi phục!')) {
                        $wire.forceDeleteItem(selectedItem, 'folder')
                    };
                    contextMenu = false
                ">
                    <span class="menu-icon">{!! tabler_icon('trash-x', ['class' => 'icon']) !!}</span>
                    <span class="menu-text">Xóa vĩnh viễn</span>
                </div>
            </div>
        </div>
    </template>
</div>

