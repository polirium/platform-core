<?php

namespace Polirium\Core\Media\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Polirium\Core\Media\Models\Media;
use Polirium\Core\Media\Services\MediaService;

class MediaManager extends Component
{
    use WithFileUploads, WithPagination;

    public $files = [];
    public $search = '';
    public $filterType = '';
    public $perPage = 30;
    public $selectedMedia = [];
    public $viewMode = 'grid';

    // Folder management
    public $currentFolder = '';
    public $folders = [];
    public $breadcrumbs = [];

    // Clipboard for cut/paste
    public $clipboard = [];
    public $clipboardFolder = '';

    // Modal states
    public $showCreateFolderModal = false;
    public $showRenameModal = false;
    public $showDetailsModal = false;
    public $showUploadModal = false;
    public $showImageEditor = false;
    public $showPreviewModal = false;
    public $previewMedia = null;

    // Modal data
    public $newFolderName = '';
    public $renameItemId = null;
    public $renameItemName = '';
    public $renameItemType = '';
    public $selectedMediaDetails = null;
    public $selectedFolderDetails = null;

    // Image editor - store ID only for Livewire compatibility
    public $editingImageId = null;
    public $resizeWidth = null;
    public $resizeHeight = null;
    public $cropX = 0;
    public $cropY = 0;
    public $cropWidth = null;
    public $cropHeight = null;

    // Context menu
    public $contextMenuVisible = false;
    public $contextMenuX = 0;
    public $contextMenuY = 0;
    public $contextMenuItemId = null;
    public $contextMenuItemType = '';

    protected $listeners = [
        'refreshMedia' => '$refresh',
        'mediaDeleted' => '$refresh',
        'closeContextMenu' => 'hideContextMenu',
    ];

    public function mount()
    {
        $this->viewMode = session('media_view_mode', 'grid');
        $this->loadFolders();
        $this->updateBreadcrumbs();
    }

    public function loadFolders()
    {
        $disk = config('media.default_disk', 'public');
        $basePath = $this->currentFolder ?: '';

        $allDirectories = Storage::disk($disk)->directories($basePath);

        // Hide system folders at root level
        $hiddenFolders = ['uploads', 'default', 'livewire-tmp'];

        $this->folders = collect($allDirectories)
            ->filter(function ($dir) use ($hiddenFolders, $basePath) {
                // Only hide at root level
                if (empty($basePath)) {
                    return !in_array(basename($dir), $hiddenFolders);
                }
                return true;
            })
            ->map(function ($dir) {
                return [
                    'name' => basename($dir),
                    'path' => $dir,
                    'type' => 'folder',
                ];
            })->toArray();
    }

    public function updateBreadcrumbs()
    {
        $this->breadcrumbs = [];
        if ($this->currentFolder) {
            $parts = explode('/', $this->currentFolder);
            $path = '';
            foreach ($parts as $part) {
                $path = $path ? $path . '/' . $part : $part;
                $this->breadcrumbs[] = [
                    'name' => $part,
                    'path' => $path,
                ];
            }
        }
    }

    public function navigateToFolder($path)
    {
        $this->currentFolder = $path;
        $this->loadFolders();
        $this->updateBreadcrumbs();
        $this->selectedMedia = [];
        $this->resetPage();
    }

    public function navigateUp()
    {
        if ($this->currentFolder) {
            $parts = explode('/', $this->currentFolder);
            array_pop($parts);
            $this->currentFolder = implode('/', $parts);
            $this->loadFolders();
            $this->updateBreadcrumbs();
            $this->resetPage();
        }
    }

    public function goToRoot()
    {
        $this->currentFolder = '';
        $this->loadFolders();
        $this->updateBreadcrumbs();
        $this->resetPage();
    }

    // Folder operations
    public function createDefaultFolder()
    {
        $disk = config('media.default_disk', 'public');
        $baseName = 'New Folder';
        $name = $baseName;
        $counter = 1;

        $path = $this->currentFolder ? $this->currentFolder . '/' . $name : $name;

        while (Storage::disk($disk)->exists($path)) {
            $name = $baseName . ' (' . $counter . ')';
            $path = $this->currentFolder ? $this->currentFolder . '/' . $name : $name;
            $counter++;
        }

        Storage::disk($disk)->makeDirectory($path);
        $this->loadFolders();

        // Return the path of the new folder to trigger inline rename
        $this->dispatch('trigger-rename', folder: $path, type: 'folder');
        session()->flash('success', 'Đã tạo folder mới!');
    }

    public function updateItemName($id, $type, $newName)
    {
        $newName = trim($newName);
        if (empty($newName)) return;

        // Validation: simple regex for folder/filenames
        if (!preg_match('/^[a-zA-Z0-9_\-\s\(\)\.]+$/', $newName)) {
             $this->addError('rename', 'Tên không hợp lệ');
             return;
        }

        $disk = config('media.default_disk', 'public');

        if ($type === 'folder') {
            $oldPath = $id;
            $parentPath = dirname($oldPath);
            // Handle root folder case where dirname is '.'
            $parentPath = $parentPath === '.' ? '' : $parentPath;

            $newPath = ($parentPath ? $parentPath . '/' : '') . $newName;

            if ($oldPath === $newPath) return;

            if (Storage::disk($disk)->exists($newPath)) {
                $this->addError('rename', 'Tên đã tồn tại');
                return;
            }

            Storage::disk($disk)->move($oldPath, $newPath);
            $this->loadFolders();
        } else {
            $media = Media::find($id);
            if ($media) {
                if ($media->name === $newName) return;

                $media->name = $newName;
                $media->save();
            }
        }
    }

    // Upload
    public function openUploadModal()
    {
        $this->showUploadModal = true;
    }

    public function updatedFiles()
    {
        $this->validate([
            'files.*' => 'file|max:' . (config('media.max_file_size', 10485760) / 1024),
        ]);

        $mediaService = app(MediaService::class);

        foreach ($this->files as $file) {
            try {
                $mediaService->upload($file, [
                    'collection' => $this->currentFolder ?: 'uploads',
                ]);
            } catch (\Exception $e) {
                $this->addError('upload', $e->getMessage());
            }
        }

        $this->files = [];
        $this->showUploadModal = false;
        session()->flash('success', 'Upload thành công!');
    }

    // Context menu
    public function showContextMenu($x, $y, $itemId, $itemType)
    {
        $this->contextMenuX = $x;
        $this->contextMenuY = $y;
        $this->contextMenuItemId = $itemId;
        $this->contextMenuItemType = $itemType;
        $this->contextMenuVisible = true;
    }

    public function hideContextMenu()
    {
        $this->contextMenuVisible = false;
    }

    // Rename
    public function openRenameModal($id, $type)
    {
        $this->renameItemId = $id;
        $this->renameItemType = $type;

        // Get name from id/path
        if ($type === 'folder') {
            $this->renameItemName = basename($id);
        } else {
            $media = Media::find($id);
            $this->renameItemName = $media ? $media->name : '';
        }

        $this->showRenameModal = true;
        $this->hideContextMenu();
    }

    public function rename()
    {
        $this->validate([
            'renameItemName' => 'required|string|max:255',
        ]);

        if ($this->renameItemType === 'folder') {
            $disk = config('media.default_disk', 'public');
            $oldPath = $this->renameItemId;
            $parentPath = dirname($oldPath);
            $newPath = ($parentPath !== '.' ? $parentPath . '/' : '') . $this->renameItemName;

            if (Storage::disk($disk)->exists($newPath)) {
                $this->addError('renameItemName', 'Folder đã tồn tại.');
                return;
            }

            Storage::disk($disk)->move($oldPath, $newPath);
            $this->loadFolders();
        } else {
            $media = Media::find($this->renameItemId);
            if ($media) {
                $media->name = $this->renameItemName;
                $media->save();
            }
        }

        $this->showRenameModal = false;
        session()->flash('success', 'Đã đổi tên thành công!');
    }

    // Delete
    public function deleteItem($id, $type)
    {
        $this->hideContextMenu();

        if ($type === 'folder') {
            $disk = config('media.default_disk', 'public');
            Storage::disk($disk)->deleteDirectory($id);
            $this->loadFolders();
            session()->flash('success', 'Đã xóa folder!');
        } else {
            $mediaService = app(MediaService::class);
            $mediaService->delete($id);
            session()->flash('success', 'Đã xóa file!');
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selectedMedia)) {
            return;
        }

        $mediaService = app(MediaService::class);
        $mediaService->deleteMultiple($this->selectedMedia);
        $this->selectedMedia = [];
        session()->flash('success', 'Đã xóa các file đã chọn!');
    }

    // Cut - add to clipboard
    public function cut($id = null)
    {
        if ($id) {
            $this->clipboard = [$id];
        } else {
            $this->clipboard = $this->selectedMedia;
        }
        $this->clipboardFolder = $this->currentFolder;
        $this->selectedMedia = [];
        $this->hideContextMenu();
        session()->flash('info', count($this->clipboard) . ' file đã được cắt. Chọn thư mục đích và nhấn Dán.');
    }

    // Paste - move files from clipboard to current folder
    public function paste()
    {
        if (empty($this->clipboard)) {
            session()->flash('error', 'Clipboard trống!');
            return;
        }

        $disk = config('media.default_disk', 'public');
        $targetFolder = $this->currentFolder ?: 'uploads';
        $movedCount = 0;

        foreach ($this->clipboard as $id) {
            $media = Media::find($id);
            if ($media) {
                try {
                    // Get current file path
                    $oldPath = $media->getPath();

                    // Create new path
                    $newDirectory = $targetFolder . '/' . date('Y/m');
                    $newPath = $newDirectory . '/' . $media->file_name;

                    // Ensure target directory exists
                    if (!Storage::disk($disk)->exists($newDirectory)) {
                        Storage::disk($disk)->makeDirectory($newDirectory);
                    }

                    // Move the physical file
                    if (Storage::disk($disk)->exists($oldPath)) {
                        Storage::disk($disk)->move($oldPath, $newPath);
                    }

                    // Update database
                    $media->collection_name = $targetFolder;
                    $customProperties = $media->custom_properties ?? [];
                    $customProperties['file_path'] = $newPath;
                    $media->custom_properties = $customProperties;
                    $media->save();
                    $movedCount++;

                } catch (\Exception $e) {
                    $this->addError('paste', 'Lỗi di chuyển file: ' . $e->getMessage());
                    \Log::error('Move file error: ' . $e->getMessage());
                }
            }
        }

        // Clear clipboard after paste
        $this->clipboard = [];
        $this->clipboardFolder = '';
        session()->flash('success', "Đã dán {$movedCount} file thành công!");
    }

    // Clear clipboard
    public function clearClipboard()
    {
        $this->clipboard = [];
        $this->clipboardFolder = '';
        session()->flash('info', 'Đã xóa clipboard.');
    }

    // Load media details for sidebar
    public function loadMediaDetails($id)
    {
        $this->selectedMediaDetails = Media::find($id);
        $this->selectedFolderDetails = null;
    }

    public function loadFolderDetails($path)
    {
        $disk = config('media.default_disk', 'public');

        // Basic info
        $name = basename($path);
        $lastModifiedTimestamp = Storage::disk($disk)->lastModified($path);
        $lastModified = $lastModifiedTimestamp ? date('d/m/Y H:i', $lastModifiedTimestamp) : '-';

        // Count items (non-recursive for performance)
        $files = Storage::disk($disk)->files($path);
        $directories = Storage::disk($disk)->directories($path);
        $itemCount = count($files) + count($directories);

        // Calculate size (this can be slow for large folders, so maybe just count is enough for now)
        // For OS-like feel, usually size is calculated on demand or cached.
        // We will stick to item count for speed.

        $this->selectedFolderDetails = [
            'name' => $name,
            'path' => $path,
            'last_modified' => $lastModified,
            'item_count' => $itemCount,
            'type' => 'Folder'
        ];
        $this->selectedMediaDetails = null;
    }

    // Details modal (legacy)
    public function showDetails($id)
    {
        $this->selectedMediaDetails = Media::find($id);
        $this->showDetailsModal = true;
        $this->hideContextMenu();
    }

    // Image Editor
    public function openImageEditor($id)
    {
        $media = Media::find($id);
        if ($media && $media->is_image) {
            $this->editingImageId = $id;
            $this->showImageEditor = true;
            $this->resizeWidth = null;
            $this->resizeHeight = null;
        }
    }

    // Computed property to get editing image model
    public function getEditingImageProperty()
    {
        if ($this->editingImageId) {
            return Media::find($this->editingImageId);
        }
        return null;
    }

    public function closeImageEditor()
    {
        $this->showImageEditor = false;
        $this->editingImageId = null;
        $this->resizeWidth = null;
        $this->resizeHeight = null;
    }

    // Preview Modal
    public function openPreviewModal($id)
    {
        $this->previewMedia = Media::find($id);
        if ($this->previewMedia) {
            $this->showPreviewModal = true;
            $this->hideContextMenu();
        }
    }

    public function closePreviewModal()
    {
        $this->showPreviewModal = false;
        $this->previewMedia = null;
    }

    public function resizeImage()
    {
        $media = Media::find($this->editingImageId);
        if (!$media || (!$this->resizeWidth && !$this->resizeHeight)) {
            session()->flash('error', 'Vui lòng nhập kích thước');
            return;
        }

        try {
            $disk = config('media.default_disk', 'public');
            $path = Storage::disk($disk)->path($media->getPath());

            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($path);

            if ($this->resizeWidth && $this->resizeHeight) {
                $image->resize((int)$this->resizeWidth, (int)$this->resizeHeight);
            } elseif ($this->resizeWidth) {
                $image->scale(width: (int)$this->resizeWidth);
            } elseif ($this->resizeHeight) {
                $image->scale(height: (int)$this->resizeHeight);
            }

            $image->save($path);

            // Update file size
            $media->size = filesize($path);
            $media->save();

            session()->flash('success', 'Đã resize ảnh thành công!');
        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi resize: ' . $e->getMessage());
            \Log::error('Resize error: ' . $e->getMessage());
        }
    }

    public function rotateImage($degrees)
    {
        $media = Media::find($this->editingImageId);
        if (!$media) {
            session()->flash('error', 'Không tìm thấy ảnh');
            return;
        }

        try {
            $disk = config('media.default_disk', 'public');
            $path = Storage::disk($disk)->path($media->getPath());

            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($path);
            $image->rotate($degrees);
            $image->save($path);

            session()->flash('success', 'Đã xoay ảnh thành công!');
            // Don't close editor - let user see result
        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi xoay: ' . $e->getMessage());
            \Log::error('Rotate error: ' . $e->getMessage());
        }
    }

    public function flipImage($direction)
    {
        $media = Media::find($this->editingImageId);
        if (!$media) {
            session()->flash('error', 'Không tìm thấy ảnh');
            return;
        }

        try {
            $disk = config('media.default_disk', 'public');
            $path = Storage::disk($disk)->path($media->getPath());

            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($path);

            if ($direction === 'h') {
                $image->flip();
            } else {
                $image->flop();
            }

            $image->save($path);

            session()->flash('success', 'Đã lật ảnh thành công!');
        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi lật: ' . $e->getMessage());
            \Log::error('Flip error: ' . $e->getMessage());
        }
    }

    public function cropImage($x, $y, $width, $height)
    {
        $media = Media::find($this->editingImageId);
        if (!$media) {
            return ['success' => false, 'message' => 'Không tìm thấy ảnh'];
        }

        try {
            $disk = config('media.default_disk', 'public');
            $path = Storage::disk($disk)->path($media->getPath());

            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($path);

            // Crop the image at given coordinates
            $image->crop((int)$width, (int)$height, (int)$x, (int)$y);
            $image->save($path);

            // Update file size
            $media->size = filesize($path);
            $media->save();

            return ['success' => true, 'message' => 'Đã cắt ảnh thành công! Kích thước mới: ' . $width . 'x' . $height];
        } catch (\Exception $e) {
            \Log::error('Crop error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi cắt: ' . $e->getMessage()];
        }
    }

    public function cropImageManual()
    {
        $media = Media::find($this->editingImageId);
        if (!$media) {
            session()->flash('error', 'Không tìm thấy ảnh');
            return;
        }

        if (!$this->cropWidth || !$this->cropHeight) {
            session()->flash('error', 'Vui lòng nhập kích thước cắt (Rộng và Cao)');
            return;
        }

        try {
            $disk = config('media.default_disk', 'public');
            $path = Storage::disk($disk)->path($media->getPath());

            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($path);

            // Crop the image
            $w = (int)$this->cropWidth;
            $h = (int)$this->cropHeight;
            $image->crop($w, $h, (int)$this->cropX, (int)$this->cropY);
            $image->save($path);

            // Update file size
            $media->size = filesize($path);
            $media->save();

            // Reset crop values
            $this->cropX = 0;
            $this->cropY = 0;
            $this->cropWidth = null;
            $this->cropHeight = null;

            session()->flash('success', 'Đã cắt ảnh thành công! Kích thước mới: ' . $w . 'x' . $h);
        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi cắt: ' . $e->getMessage());
            \Log::error('Crop error: ' . $e->getMessage());
        }
    }

    public function saveImageEdits()
    {
        $this->closeImageEditor();
        session()->flash('success', 'Đã lưu thay đổi!');
    }

    // Copy URL
    public function copyUrl($id)
    {
        $media = Media::find($id);
        if ($media) {
            $this->dispatch('copyToClipboard', url: $media->getUrl());
        }
        $this->hideContextMenu();
    }

    // Download
    public function download($id)
    {
        $media = Media::find($id);
        if ($media) {
            return response()->download(
                Storage::disk($media->disk)->path($media->getPath()),
                $media->file_name
            );
        }
        $this->hideContextMenu();
    }

    // View mode
    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        session(['media_view_mode' => $mode]);
    }

    // Selection
    public function toggleSelect($id)
    {
        if (in_array($id, $this->selectedMedia)) {
            $this->selectedMedia = array_diff($this->selectedMedia, [$id]);
        } else {
            $this->selectedMedia[] = $id;
        }
    }

    public function selectAll()
    {
        $this->selectedMedia = $this->getMediaQuery()->pluck('id')->toArray();
    }

    public function clearSelection()
    {
        $this->selectedMedia = [];
    }

    protected function getMediaQuery()
    {
        $query = Media::query()->orderBy('created_at', 'desc');

        // Filter by current folder/collection
        // At root level, show files with 'uploads' collection or no specific collection
        // In a folder, show files with that folder as collection
        if ($this->currentFolder) {
            $query->where('collection_name', $this->currentFolder);
        } else {
            // At root, only show files in 'uploads' or 'default' collection
            // Don't show files that belong to other folders/collections
            $disk = config('media.default_disk', 'public');
            $existingFolders = \Storage::disk($disk)->directories();

            // Exclude files that are in specific folders
            if (!empty($existingFolders)) {
                $query->where(function($q) use ($existingFolders) {
                    $q->whereIn('collection_name', ['uploads', 'default', ''])
                      ->orWhereNull('collection_name');
                });
            }
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('file_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterType) {
            switch ($this->filterType) {
                case 'image':
                    $query->images();
                    break;
                case 'video':
                    $query->videos();
                    break;
                case 'document':
                    $query->documents();
                    break;
                case 'audio':
                    $query->audio();
                    break;
            }
        }

        return $query;
    }

    public function render()
    {
        $media = $this->getMediaQuery()->paginate($this->perPage);

        // Get all folders for move modal
        $disk = config('media.default_disk', 'public');
        $allFolders = Storage::disk($disk)->allDirectories();

        return view('core/media::livewire.media-manager', [
            'mediaItems' => $media,
            'allFolders' => $allFolders,
            'editingImage' => $this->editingImage,
        ]);
    }
}
