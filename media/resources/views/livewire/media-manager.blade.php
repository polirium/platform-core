@props(['search', 'filterType', 'viewMode', 'breadcrumbs', 'currentFolder', 'clipboard', 'selectedMedia', 'selectedMediaDetails', 'mediaItems', 'folders'])

<div x-data="{
    contextMenu: false,
    contextX: 0,
    contextY: 0,
    selectedItem: null,
    selectedType: null,
    selectedIsImage: false,
    activeItemId: null,
    activeType: null,
    showSidebar: false,
    sidebarItem: null,
    sidebarType: null,
    renamingId: null,
    renamingType: null,

    selectedMediaIds: @entangle('selectedMedia'),
    draggingItems: [],
    draggingType: null,
    dropTarget: null,

    isSelecting: false,
    selectionStart: { x: 0, y: 0 },
    selectionBox: { style: '', x: 0, y: 0, w: 0, h: 0 },

    // Drag-drop upload from OS
    isDraggingFiles: false,
    dragCounter: 0,

    // Initialize and watch sidebar state
    init() {
        // Watch sidebar state for body scroll lock on mobile
        this.$watch('showSidebar', (value) => {
            if (window.innerWidth < 768) {
                if (value) {
                    document.body.style.overflow = 'hidden';
                    document.body.classList.add('sidebar-open');
                } else {
                    document.body.style.overflow = '';
                    document.body.classList.remove('sidebar-open');
                }
            }
        });
    },

    // Long-press selection for mobile
    longPressTimer: null,
    longPressDuration: 500, // ms

    handleTouchStart(rawId, event) {
        // Only start long-press timer on touch devices
        if (!('ontouchstart' in window)) return;

        this.longPressTimer = setTimeout(() => {
            // Toggle selection on long press
            let id = isNaN(rawId) ? rawId : parseInt(rawId);

            if (this.selectedMediaIds.includes(id)) {
                this.selectedMediaIds = this.selectedMediaIds.filter(i => i !== id);
            } else {
                this.selectedMediaIds.push(id);
            }

            // Haptic feedback if available
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        }, this.longPressDuration);
    },

    handleTouchEnd(event) {
        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
    },

    handleTouchMove(event) {
        // Cancel long press if finger moves
        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
    },

    handleFileDragEnter(e) {
        e.preventDefault();
        this.dragCounter++;
        if (e.dataTransfer.types.includes('Files')) {
            this.isDraggingFiles = true;
        }
    },

    handleFileDragLeave(e) {
        e.preventDefault();
        this.dragCounter--;
        if (this.dragCounter === 0) {
            this.isDraggingFiles = false;
        }
    },

    handleFileDrop(e) {
        e.preventDefault();
        this.isDraggingFiles = false;
        this.dragCounter = 0;

        // Get files from drop event
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            // Use the hidden file input to trigger Livewire upload
            const input = document.getElementById('media-upload-input');
            const dataTransfer = new DataTransfer();
            for (let i = 0; i < files.length; i++) {
                dataTransfer.items.add(files[i]);
            }
            input.files = dataTransfer.files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    },

    selectItem(rawId, event) {
        // Prevent conflict with Context Menu or other actions if needed
        let isMulti = event.metaKey || event.ctrlKey;
        let id = isNaN(rawId) ? rawId : parseInt(rawId);

        if (isMulti) {
            if (this.selectedMediaIds.includes(id)) {
                this.selectedMediaIds = this.selectedMediaIds.filter(i => i !== id);
            } else {
                this.selectedMediaIds.push(id);
            }
        } else {
            // Single click selects ONLY this item
            this.selectedMediaIds = [id];
        }
    },

    handleDragStart(id, type) {
        if (!this.selectedMediaIds.includes(parseInt(id)) && !this.selectedMediaIds.includes(String(id))) {
            this.selectedMediaIds = [id];
        }

        this.draggingItems = this.selectedMediaIds;
        this.draggingType = type;
    },

    handleDrop(targetPath) {
        if (targetPath === null) return;

        this.dropTarget = null;
        this.draggingItems = [];

        this.$wire.moveSelectedTo(targetPath);
    },

    startSelection(e) {
        // Only start if we are clicking on the main content area or grid gaps
        // Allow starting on media-content, media-grid-container, media-grid
        if (e.target.closest('.media-grid-item') ||
            e.target.closest('.media-list-item') ||
            e.target.closest('.media-toolbar') ||
            e.target.closest('.media-sidebar')) return;

        this.isSelecting = true;
        this.selectionStart = { x: e.clientX, y: e.clientY };
        this.selectionBox = { style: 'display:block;', x: e.clientX, y: e.clientY, w: 0, h: 0 };
    },

    updateSelection(e) {
        if (!this.isSelecting) return;

        let currentX = e.clientX;
        let currentY = e.clientY;

        let x = Math.min(this.selectionStart.x, currentX);
        let y = Math.min(this.selectionStart.y, currentY);
        let w = Math.abs(currentX - this.selectionStart.x);
        let h = Math.abs(currentY - this.selectionStart.y);

        this.selectionBox = {
            style: `display:block; left:${x}px; top:${y}px; width:${w}px; height:${h}px;`,
            x: x, y: y, w: w, h: h
        };
    },

    endSelection(e) {
        if (!this.isSelecting) return;
        this.isSelecting = false;

        let box = this.selectionBox;
        let items = document.querySelectorAll('.media-grid-item');
        let newSelection = [];

        if (!e.shiftKey && !e.metaKey) {
             newSelection = [];
        } else {
             newSelection = [...this.selectedMediaIds];
        }

        items.forEach(el => {
            if (el.classList.contains('media-back-btn')) return;

            let rect = el.getBoundingClientRect();
            if (box.x < rect.right && box.x + box.w > rect.left &&
                box.y < rect.bottom && box.y + box.h > rect.top) {

                let rawId = el.dataset.itemId;
                if (rawId) {
                    let id = isNaN(rawId) ? rawId : parseInt(rawId);

                    if (!newSelection.includes(id)) newSelection.push(id);
                }
            }
        });

        this.selectedMediaIds = newSelection;
        this.selectionBox.style = 'display:none;';
    },
}"
@trigger-rename.window="
    // Clear any existing rename state first
    renamingId = null;
    renamingType = null;

    // Use $nextTick to wait for DOM update
    $nextTick(() => {
        renamingId = $event.detail.folder;
        renamingType = $event.detail.type;

        // Then wait for input to be visible
        $nextTick(() => {
            let inputs = document.querySelectorAll('[data-rename-input]');
            for (let i = 0; i < inputs.length; i++) {
                let input = inputs[i];
                if ($event.detail.type === 'folder' && input.dataset.folderPath === $event.detail.folder) {
                    // Clear and reset the input value to ensure it's fresh from server
                    input.value = '';
                    input.focus();
                    // Select all text after a small delay to ensure value is loaded
                    setTimeout(() => {
                        input.select();
                    }, 10);
                    break;
                }
                if ($event.detail.type === 'file' && input.dataset.itemId == $event.detail.folder) {
                    input.value = '';
                    input.focus();
                    setTimeout(() => {
                        input.select();
                    }, 10);
                    break;
                }
            }
        });
    });
"
@scroll-to-folder.window="
    setTimeout(() => {
        const folderPath = $event.detail.folderPath;
        const folderElement = document.querySelector(`[data-item-id='folder:${folderPath}']`);
        if (folderElement) {
            folderElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // On mobile, also scroll the breadcrumb if needed
            if (window.innerWidth < 768) {
                const breadcrumb = document.querySelector('.media-mobile-breadcrumb');
                if (breadcrumb) {
                    breadcrumb.scrollLeft = breadcrumb.scrollWidth;
                }
            }
        }
    }, 200);
"
@click="
    if(!$event.target.closest('.media-context-menu') && !$event.target.closest('.media-grid-item') && !$event.target.closest('.folder-item') && !$event.target.closest('.media-list-item')) { contextMenu = false }
    if(!$event.target.closest('.media-sidebar') && !$event.target.closest('.media-grid-item') && !$event.target.closest('.folder-item') && !$event.target.closest('.media-list-item')) { showSidebar = false }
"
@keydown.escape.window="contextMenu = false; showSidebar = false"
@selection-cleared.window="selectedMediaIds = []"
class="media-manager">

    {{-- Hidden Upload Input --}}
    <input type="file" id="media-upload-input" wire:model="files" multiple class="d-none"
           accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.txt">

    {{-- Marquee Selection Box (Fixed Position) --}}
    <div class="selection-marquee" :style="selectionBox.style"></div>

    {{-- Main Layout Container --}}
    <div class="media-layout"
         @dragenter="handleFileDragEnter($event)"
         @dragleave="handleFileDragLeave($event)"
         @dragover.prevent
         @drop.prevent="handleFileDrop($event)">

        {{-- Drop Overlay --}}
        <div x-show="isDraggingFiles"
             x-transition
             class="drop-overlay">
            <div class="drop-overlay-content">
                {!! tabler_icon('upload', ['class' => 'icon icon-lg']) !!}
                <span>Thả file để upload</span>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="media-content"
             :class="showSidebar ? 'sidebar-open' : ''"
             @mousedown="startSelection($event)"
             @mousemove.window="updateSelection($event)"
             @mouseup.window="endSelection($event)"
             @contextmenu.self.prevent="contextMenu = true; contextX = $event.clientX; contextY = $event.clientY; selectedType = 'background'; selectedItem = null"
             style="user-select: none;">

            {{-- Toolbar --}}
            @include('core/media::components.toolbar', [
                'search' => $search,
                'filterType' => $filterType,
                'viewMode' => $viewMode,
                'breadcrumbs' => $breadcrumbs,
                'currentFolder' => $currentFolder,
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

            {{-- Selection Bar - REMOVED, now using Context Menu for actions --}}

            {{-- Upload Progress Overlay --}}
            <div wire:loading wire:target="files" class="upload-progress-overlay">
                <div class="upload-progress-card">
                    <div class="upload-progress-spinner">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    <div class="upload-progress-text">
                        <strong>Đang upload...</strong>
                        <small class="text-muted d-block">Vui lòng đợi trong giây lát</small>
                    </div>
                </div>
            </div>

            {{-- Toast Notifications --}}
            @if(session('success'))
                <div class="media-toast media-toast-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                    <span class="toast-icon">{!! tabler_icon('check', ['class' => 'icon']) !!}</span>
                    <span class="toast-message">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="media-toast media-toast-error" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
                    <span class="toast-icon">{!! tabler_icon('x', ['class' => 'icon']) !!}</span>
                    <span class="toast-message">{{ session('error') }}</span>
                </div>
            @endif
            @if(session('info'))
                <div class="media-toast media-toast-info" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                    <span class="toast-icon">{!! tabler_icon('info-circle', ['class' => 'icon']) !!}</span>
                    <span class="toast-message">{{ session('info') }}</span>
                </div>
            @endif

            {{-- Content Area --}}
            @if($viewMode === 'grid')
                <div class="media-grid-container px-3">
                    @if($mediaItems->isEmpty() && count($folders) === 0 && !$currentFolder)
                        <div class="media-empty">
                            <div class="media-empty-icon">
                                {!! tabler_icon('photo', ['class' => 'icon']) !!}
                            </div>
                            <p class="media-empty-title">{{ __('core/media::media.no_files') }}</p>
                            <p class="media-empty-subtitle">{{ __('core/media::media.no_files_hint') }}</p>
                        </div>
                    @else
                        <div class="media-grid">
                            {{-- Back Button - Hide in Trash view --}}
                            @if(!$showTrash && $currentFolder)
                                <div class="media-grid-item media-back-btn" wire:click="navigateUp">
                                    <div class="media-thumbnail">
                                        {!! tabler_icon('arrow-left', ['class' => 'icon-back']) !!}
                                    </div>
                                    <div class="media-info">
                                    <div class="media-name">{{ __('core/media::media.back') }}</div>
                                    </div>
                                </div>
                            @endif

                            {{-- Normal Folders - Hide in Trash view --}}
                            @if(!$showTrash)
                            @foreach($folders as $folder)
                                <div class="media-grid-item folder-item"
                                     data-item-id="folder:{{ $folder['path'] }}"
                                     draggable="true"
                                     @dragstart="handleDragStart('folder:{{ $folder['path'] }}', 'folder')"
                                     @dragend="dropTarget = null; draggingItems = []"
                                     @dragover.prevent="if(draggingItems.length > 0 && !draggingItems.includes('folder:{{ $folder['path'] }}')) dropTarget = '{{ $folder['path'] }}'"
                                     @dragleave.prevent="dropTarget = null"
                                     @drop.prevent="handleDrop('{{ $folder['path'] }}')"
                                     @touchstart="handleTouchStart('folder:{{ $folder['path'] }}', $event)"
                                     @touchend="handleTouchEnd($event)"
                                     @touchmove="handleTouchMove($event)"
                                     :class="{
                                         'active': activeItemId === '{{ $folder['path'] }}' && activeType === 'folder',
                                         'drop-active': dropTarget === '{{ $folder['path'] }}',
                                         'dragging': draggingItems.includes('folder:{{ $folder['path'] }}'),
                                         'selected': selectedMediaIds.includes('folder:{{ $folder['path'] }}')
                                     }"
                                     @click="
                                         selectItem('folder:{{ $folder['path'] }}', $event);
                                         // Debounce sidebar show to prevent flicker on double-click
                                         clearTimeout(window.folderClickTimeout);
                                         window.folderClickTimeout = setTimeout(() => {
                                             showSidebar = true;
                                             sidebarType = 'folder';
                                             $wire.loadFolderDetails('{{ $folder['path'] }}');
                                         }, 200);
                                         activeItemId = '{{ $folder['path'] }}';
                                         activeType = 'folder'
                                     "
                                     @dblclick="
                                         clearTimeout(window.folderClickTimeout);
                                         $wire.navigateToFolder('{{ $folder['path'] }}')
                                     "
                                     @contextmenu.prevent="contextMenu = true; contextX = $event.clientX; contextY = $event.clientY; selectedItem = '{{ $folder['path'] }}'; selectedType = 'folder'; selectedIsImage = false">
                                    <div class="media-thumbnail">
                                        {!! tabler_icon('folder', ['class' => 'icon-folder']) !!}
                                    </div>
                                    <div class="media-info">
                                        <div x-show="renamingId !== '{{ $folder['path'] }}' || renamingType !== 'folder'" class="media-name" title="{{ $folder['name'] }}">{{ Str::limit($folder['name'], 12) }}</div>
                                        <input x-show="renamingId === '{{ $folder['path'] }}' && renamingType === 'folder'"
                                               data-rename-input="true"
                                               data-folder-path="{{ $folder['path'] }}"
                                               type="text"
                                               class="form-control form-control-sm text-center p-0 h-auto"
                                               :value="'{{ $folder['name'] }}'"
                                               @click.stop
                                               @dblclick.stop
                                               @keydown.enter="$el.blur()"
                                               @keydown.escape="renamingId = null"
                                               @blur="$wire.updateItemName('{{ $folder['path'] }}', 'folder', $el.value); renamingId = null">
                                        <div class="media-meta">{{ __('core/media::media.folder') }}</div>
                                    </div>
                                </div>
                            @endforeach
                            @endif

                            {{-- Trash Folders - Virtual folders from trashed files --}}
                            @if($showTrash)
                                {{-- Back button in Trash view --}}
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

                                @foreach($trashFolders as $folder)
                                    <div class="media-grid-item folder-item trash-folder"
                                         data-item-id="folder:{{ $folder['path'] }}"
                                         :class="{
                                             'selected': selectedMediaIds.includes('folder:{{ $folder['path'] }}')
                                         }"
                                         style="opacity: 0.7;"
                                         @click="
                                             selectItem('folder:{{ $folder['path'] }}', $event);
                                         "
                                         @dblclick="$wire.navigateToFolder('{{ $folder['path'] }}')"
                                         @contextmenu.prevent="
                                             contextMenu = true;
                                             contextX = $event.clientX;
                                             contextY = $event.clientY;
                                             selectedItem = {{ $folder['id'] }};
                                             selectedType = 'trashed-folder';
                                             selectedIsImage = false
                                         ">
                                        <div class="media-thumbnail">
                                            {!! tabler_icon('folder', ['class' => 'icon-folder text-danger']) !!}
                                        </div>
                                        <div class="media-info">
                                            <div class="media-name" title="{{ $folder['name'] }}">{{ Str::limit($folder['name'], 12) }}</div>
                                            <div class="media-meta text-danger">Đã xóa</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            {{-- Files --}}
                            @foreach($mediaItems as $item)
                                <div class="media-grid-item {{ in_array($item->id, $selectedMedia) ? 'selected' : '' }}"
                                     data-item-id="{{ $item->id }}"
                                     :class="{
                                         'active': activeItemId === {{ $item->id }} && activeType === 'file',
                                         'dragging': draggingItems.includes({{ $item->id }}) || draggingItems.includes('{{ $item->id }}'),
                                         'selected': selectedMediaIds.includes({{ $item->id }})
                                     }"
                                     draggable="true"
                                     @dragstart="handleDragStart({{ $item->id }}, 'file')"
                                     @dragend="dropTarget = null; draggingItems = []"
                                     @touchstart="handleTouchStart({{ $item->id }}, $event)"
                                     @touchend="handleTouchEnd($event)"
                                     @touchmove="handleTouchMove($event)"
                                     data-media-id="{{ $item->id }}"
                                     data-url="{{ $item->getSecureUrl() }}"
                                     @click="
                                         selectItem({{ $item->id }}, $event);
                                         // Only load sidebar if clicking on a different file
                                         if (activeItemId !== {{ $item->id }} || activeType !== 'file') {
                                             showSidebar = true;
                                             sidebarType = 'file';
                                             $wire.loadMediaDetails({{ $item->id }});
                                         }
                                         activeItemId = {{ $item->id }};
                                         activeType = 'file'
                                     "
                                     @contextmenu.prevent="
                                         contextMenu = true;
                                         contextX = $event.clientX;
                                         contextY = $event.clientY;
                                         // Only switch selection if right-clicking a non-selected item
                                         if (!selectedMediaIds.includes({{ $item->id }})) {
                                             $wire.call('clearSelection'); // Optional: clear others or just add? User usually expects select single if right-clicking outside.
                                             // Actually, standard OS behavior: if right click outside selection -> select that single item.
                                             selectedMediaIds = [{{ $item->id }}];
                                         }
                                         selectedItem = {{ $item->id }};
                                         selectedType = '{{ $showTrash ? 'trashed' : 'file' }}';
                                         selectedIsImage = {{ $item->is_image ? 'true' : 'false' }}
                                     ">

                                     <div class="media-thumbnail">
                                        @if($item->is_image)
                                            <img src="{{ $item->getSecureUrl() }}" alt="{{ $item->name }}" loading="lazy">
                                        @elseif($item->is_video)
                                            {!! tabler_icon('video', ['class' => 'icon-media']) !!}
                                        @elseif($item->is_document)
                                            {!! tabler_icon('file-text', ['class' => 'icon-media']) !!}
                                        @else
                                            {!! tabler_icon('file', ['class' => 'icon-media']) !!}
                                        @endif
                                    </div>

                                    <div class="media-info">
                                        <div x-show="renamingId !== {{ $item->id }} || renamingType !== 'file'" class="media-name" title="{{ $item->file_name }}">{{ Str::limit($item->name, 12) }}</div>
                                        <input x-show="renamingId === {{ $item->id }} && renamingType === 'file'"
                                               data-rename-input="true"
                                               data-item-id="{{ $item->id }}"
                                               type="text"
                                               class="form-control form-control-sm text-center p-0 h-auto"
                                               value="{{ $item->name }}"
                                               @click.stop
                                               @dblclick.stop
                                               @keydown.enter="$el.blur()"
                                               @keydown.escape="renamingId = null"
                                               @blur="$wire.updateItemName({{ $item->id }}, 'file', $el.value); renamingId = null">
                                        <div class="media-meta">{{ $item->formatted_size }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                {{-- List View --}}
                <div class="media-table-container">
                    <table class="table table-hover table-vcenter media-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                            <tr>
                                <th style="width: 20px;">
                                    <label class="form-check m-0">
                                        <input type="checkbox" class="form-check-input" wire:click="selectAll">
                                    </label>
                                </th>
                                <th style="width: 50px;"></th> {{-- Thumbnail --}}
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
                                    @click="showSidebar = true; sidebarType = 'folder'; $wire.loadFolderDetails('{{ $folder['path'] }}')"
                                    @contextmenu.prevent="contextMenu = true; contextX = $event.clientX; contextY = $event.clientY; selectedItem = '{{ $folder['path'] }}'; selectedType = 'folder'"
                                    class="media-list-item cursor-pointer">
                                    <td></td>
                                    <td>
                                        <div class="media-list-thumbnail folder-thumbnail">
                                            {!! tabler_icon('folder', ['class' => 'icon-folder']) !!}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-medium">{{ $folder['name'] }}</div>
                                    </td>
                                    <td class="text-muted">{{ __('core/media::media.folder') }}</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @endforeach

                            @forelse($mediaItems as $item)
                                <tr @click="$wire.loadMediaDetails({{ $item->id }}); showSidebar = true"
                                    @contextmenu.prevent="contextMenu = true; contextX = $event.clientX; contextY = $event.clientY; selectedItem = {{ $item->id }}; selectedType = '{{ $showTrash ? 'trashed' : 'file' }}'; selectedIsImage = {{ $item->is_image ? 'true' : 'false' }}"
                                    class="media-list-item cursor-pointer {{ in_array($item->id, $selectedMedia) ? 'table-primary' : '' }}">
                                    <td @click.stop>
                                        {{-- Checkbox Removed --}}
                                    </td>
                                    <td>
                                        <div class="media-list-thumbnail">
                                            @if($item->is_image)
                                                <img src="{{ $item->getSecureUrl() }}" alt="{{ $item->name }}" loading="lazy">
                                            @elseif($item->is_video)
                                                {!! tabler_icon('video', ['class' => 'icon-media']) !!}
                                            @elseif($item->is_document)
                                                {!! tabler_icon('file-text', ['class' => 'icon-media']) !!}
                                            @else
                                                {!! tabler_icon('file', ['class' => 'icon-media']) !!}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-medium">{{ $item->name }}</div>
                                        <div class="text-muted small d-block d-md-none">{{ $item->formatted_size }}</div>
                                    </td>
                                    <td class="text-muted">{{ $item->mime_type }}</td>
                                    <td>{{ $item->formatted_size }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                @if(count($folders) === 0)
                                    <tr><td colspan="6" class="text-center text-muted py-4">{{ __('core/media::media.no_data') }}</td></tr>
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
        @include('core/media::components.sidebar', [
            'selectedMedia' => $selectedMediaDetails,
            'selectedFolder' => $selectedFolderDetails
        ])
    </div>

    {{-- Context Menu --}}
    @include('core/media::components.context-menu')

    {{-- Modals --}}
    @include('core/media::components.modals.create-folder')
    @include('core/media::components.modals.rename')
    @include('core/media::components.modals.image-editor')
    @include('core/media::components.modals.preview')
</div>

</div>
