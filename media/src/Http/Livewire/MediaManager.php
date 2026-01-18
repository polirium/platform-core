<?php

namespace Polirium\Core\Media\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Polirium\Core\Media\Models\Media;
use Polirium\Core\Media\Models\MediaFolder;
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
    public $showTrash = false; // Toggle trash view

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
        // Root folder is 'uploads' - all media content is under this
        $rootPath = 'uploads';

        if ($this->showTrash) {
            // In trash view, load trashed folders from DB
            $this->folders = $this->loadTrashFolders();
            return;
        }

        // Load folders from database
        if (empty($this->currentFolder) || $this->currentFolder === 'uploads') {
            // At root level - get root folders from DB
            $dbFolders = MediaFolder::root()->get();
        } else {
            // In a subfolder - get children from DB
            $parent = MediaFolder::where('path', $this->currentFolder)->first();
            $dbFolders = $parent ? $parent->children : collect();
        }

        $this->folders = $dbFolders->map(function ($folder) {
            return [
                'id' => $folder->id,
                'name' => $folder->name,
                'path' => $folder->path,
                'type' => 'folder',
            ];
        })->toArray();
    }

    /**
     * Load trashed folders from database.
     * Returns folders at the current level that are in trash.
     */
    public function loadTrashFolders()
    {
        // Load trashed folders from database
        if (empty($this->currentFolder) || $this->currentFolder === 'uploads') {
            // At root level - get trashed root folders
            $trashedFolders = MediaFolder::onlyTrashed()->root()->get();
        } else {
            // In a subfolder - get trashed children
            // First check if parent exists (may be trashed or not)
            $parent = MediaFolder::withTrashed()->where('path', $this->currentFolder)->first();
            $trashedFolders = $parent
                ? MediaFolder::onlyTrashed()->where('parent_id', $parent->id)->get()
                : collect();
        }

        return $trashedFolders->map(function ($folder) {
            return [
                'id' => $folder->id,
                'name' => $folder->name,
                'path' => $folder->path,
                'type' => 'folder',
                'trashed' => true,
            ];
        })->toArray();
    }

    public function updateBreadcrumbs()
    {
        $this->breadcrumbs = [];
        if ($this->currentFolder && $this->currentFolder !== 'uploads') {
            $parts = explode('/', $this->currentFolder);
            $path = '';
            foreach ($parts as $part) {
                // Skip "uploads" in breadcrumb display - it's the root represented by home icon
                if ($part === 'uploads') {
                    $path = 'uploads';
                    continue;
                }
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

        // Determine parent folder
        $parentFolder = null;
        if (!empty($this->currentFolder) && $this->currentFolder !== 'uploads') {
            $parentFolder = MediaFolder::where('path', $this->currentFolder)->first();
        }

        // Build path
        $basePath = $this->currentFolder ?: 'uploads';
        $path = $basePath . '/' . $name;

        // Check for existing folders (by path in DB)
        while (MediaFolder::where('path', $path)->exists()) {
            $name = $baseName . ' (' . $counter . ')';
            $path = $basePath . '/' . $name;
            $counter++;
        }

        // Create folder record in DB
        $folder = MediaFolder::create([
            'name' => $name,
            'path' => $path,
            'parent_id' => $parentFolder?->id,
        ]);

        // Create physical directory
        if (!Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->makeDirectory($path);
        }

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
            // Find folder by path in DB
            $folder = MediaFolder::where('path', $id)->first();
            if (!$folder) return;

            $oldPath = $folder->path;
            $parentPath = dirname($oldPath);
            $parentPath = $parentPath === '.' ? 'uploads' : $parentPath;

            $newPath = $parentPath . '/' . $newName;

            if ($oldPath === $newPath) return;

            // Check if new path already exists
            if (MediaFolder::where('path', $newPath)->exists()) {
                $this->addError('rename', 'Tên đã tồn tại');
                return;
            }

            // Update DB record
            $folder->name = $newName;
            $folder->path = $newPath;
            $folder->save();

            // Update all child folders' paths
            $this->updateChildFolderPaths($folder, $oldPath, $newPath);

            // Update all files in this folder
            Media::where('collection_name', $oldPath)->update(['collection_name' => $newPath]);
            Media::where('collection_name', 'like', $oldPath . '/%')
                ->get()
                ->each(function ($file) use ($oldPath, $newPath) {
                    $file->collection_name = str_replace($oldPath, $newPath, $file->collection_name);
                    $file->save();
                });

            // Rename physical directory
            if (Storage::disk($disk)->exists($oldPath)) {
                Storage::disk($disk)->move($oldPath, $newPath);
            }

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

    /**
     * Recursively update child folder paths when parent is renamed.
     */
    protected function updateChildFolderPaths(MediaFolder $folder, string $oldPath, string $newPath)
    {
        foreach ($folder->children as $child) {
            $child->path = str_replace($oldPath, $newPath, $child->path);
            $child->save();
            $this->updateChildFolderPaths($child, $oldPath, $newPath);
        }
    }

    /**
     * Recursively update child folder paths when parent is moved.
     */
    protected function updateMovedFolderPaths(int $parentId, string $oldPath, string $newPath)
    {
        $children = MediaFolder::where('parent_id', $parentId)->get();
        foreach ($children as $child) {
            $child->path = str_replace($oldPath, $newPath, $child->path);
            $child->save();
            $this->updateMovedFolderPaths($child->id, $oldPath, $newPath);
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

        // Clear selection on backend
        $this->selectedMedia = [];
        // Dispatch event to clear selection on frontend (Alpine)
        $this->dispatch('selection-cleared');
    }

    public function deleteSelected()
    {
        if (empty($this->selectedMedia)) {
            return;
        }

        $fileCount = 0;
        $folderCount = 0;

        foreach ($this->selectedMedia as $id) {
            // Handle Folders (prefixed with 'folder:')
            if (is_string($id) && str_starts_with($id, 'folder:')) {
                $folderPath = trim(substr($id, 7), '/');

                // Find folder in DB and soft delete it
                $folder = MediaFolder::where('path', $folderPath)->first();
                if ($folder) {
                    // Soft delete folder and all its children recursively
                    $this->softDeleteFolderRecursive($folder);
                    $folderCount++;
                }
            }
            // Handle Files (numeric IDs) - SOFT DELETE ONLY
            elseif (is_numeric($id)) {
                $media = Media::find($id);
                if ($media) {
                    $media->delete(); // Soft delete
                    $fileCount++;
                }
            }
        }

        $this->selectedMedia = [];
        $this->loadFolders();
        $this->dispatch('selection-cleared');

        $message = [];
        if ($fileCount > 0) $message[] = "{$fileCount} file";
        if ($folderCount > 0) $message[] = "{$folderCount} folder";
        session()->flash('success', 'Đã xóa ' . implode(' và ', $message) . '!');
    }

    /**
     * Recursively soft delete folder and all its contents.
     */
    protected function softDeleteFolderRecursive(MediaFolder $folder)
    {
        // Soft delete all child folders recursively
        foreach ($folder->children as $child) {
            $this->softDeleteFolderRecursive($child);
        }

        // Soft delete all files in this folder
        Media::where('collection_name', $folder->path)->delete();

        // Soft delete the folder itself
        $folder->delete();
    }

    // Cut - add to clipboard
    public function cut($ids = null)
    {
        if ($ids !== null) {
            // Ensure $ids is always an array
            $this->clipboard = is_array($ids) ? $ids : [$ids];
        } else {
            $this->clipboard = $this->selectedMedia;
        }
        $this->clipboardFolder = $this->currentFolder;
        $this->selectedMedia = [];
        $this->dispatch('selection-cleared'); // Sync with frontend
        $this->hideContextMenu();
        session()->flash('info', count($this->clipboard) . ' file đã được cắt. Vào thư mục đích và chuột phải -> Dán.');
    }

    // Move selected items to a specific folder (Drag & Drop)
    public function moveSelectedTo($targetPath)
    {
        if (empty($this->selectedMedia)) {
            return;
        }

        // Clean target path
        $targetPath = trim($targetPath, '/');
        $targetPath = $targetPath === '' ? '' : $targetPath;

        $disk = config('media.default_disk', 'public');
        $storage = Storage::disk($disk);
        $movedCount = 0;

        foreach ($this->selectedMedia as $id) {
            // Handle Files (Int ID)
            if (is_numeric($id)) {
                $media = Media::find($id);
                if ($media) {
                    try {
                        if ($media->collection_name === $targetPath) continue;

                        $oldPath = $media->getPath();
                        // Desired: "Folder/File.ext" (No date structure)
                        $newPath = ($targetPath ? $targetPath . '/' : '') . $media->file_name;

                        // Collision check & Auto-rename
                        if ($storage->exists($newPath)) {
                            $nameInfo = pathinfo($media->file_name);
                            $counter = 1;
                            while ($storage->exists($newPath)) {
                                $newFileName = $nameInfo['filename'] . " ($counter)." . $nameInfo['extension'];
                                $newPath = ($targetPath ? $targetPath . '/' : '') . $newFileName;
                                $counter++;
                            }
                            // Update filename in DB model to match new physical name
                            $media->file_name = basename($newPath); // This will be saved below
                        }

                        // Move physical file
                        if ($storage->exists($oldPath)) {
                            $storage->move($oldPath, $newPath);
                        }

                        // Update DB
                        $media->collection_name = $targetPath;

                        // FIX 404: Override default date-based getPath() by saving explicit path
                        $customProperties = $media->custom_properties;
                        $customProperties['file_path'] = $newPath;
                        $media->custom_properties = $customProperties;

                        $media->save(); // Saves new collection_name and potentially new file_name and properties
                    } catch (\Exception $e) {
                         \Log::error("File move failed ID $id: " . $e->getMessage());
                    }
                }
            }
            // Handle Folders (prefixed with 'folder:')
            elseif (is_string($id) && str_starts_with($id, 'folder:')) {
                $oldFolderPath = trim(substr($id, 7), '/'); // Remove 'folder:' prefix
                if ($oldFolderPath === $targetPath) continue; // Move to self?

                // Prevent circular move (Parent into Child)
                // If Target STARTS WITH Source, it's invalid.
                // e.g. Source: "A", Target: "A/B". Moving A into B is impossible.
                if ($targetPath === $oldFolderPath || str_starts_with($targetPath . '/', $oldFolderPath . '/')) {
                    session()->flash('error', "Không thể di chuyển folder '$oldFolderPath' vào bên trong chính nó.");
                    continue;
                }

                $folderName = basename($oldFolderPath);
                $newFolderPath = ($targetPath ? $targetPath . '/' : '') . $folderName;

                try {
                    // Collision check (Folders)
                    if ($storage->exists($newFolderPath)) {
                         // Auto-rename folder? Or fail? OS usually merges or renames.
                         // Let's auto-rename for safety.
                        $counter = 1;
                        while ($storage->exists($newFolderPath)) {
                            $newFolderName = $folderName . " ($counter)";
                            $newFolderPath = ($targetPath ? $targetPath . '/' : '') . $newFolderName;
                            $counter++;
                        }
                    }

                    // Move physical directory
                    $physicalMoveExisted = false;
                    if ($storage->exists($oldFolderPath)) {
                        $storage->move($oldFolderPath, $newFolderPath);
                        $physicalMoveExisted = true;
                    } elseif ($storage->exists($newFolderPath)) {
                        // RECOVERY MODE:
                        // Source missing, but Destination exists?
                        // It means a previous move likely crashed before DB update.
                        // We proceed to update DB to sync state.
                        $physicalMoveExisted = true;
                    }

                    if ($physicalMoveExisted) {
                        // Update MediaFolder record in DB
                        $folder = MediaFolder::where('path', $oldFolderPath)->first();
                        if ($folder) {
                            // Find new parent folder
                            $newParent = $targetPath ? MediaFolder::where('path', $targetPath)->first() : null;

                            // Update folder record
                            $folder->path = $newFolderPath;
                            $folder->name = basename($newFolderPath);
                            $folder->parent_id = $newParent?->id;
                            $folder->save();

                            // Update all child folder paths recursively
                            $this->updateMovedFolderPaths($folder->id, $oldFolderPath, $newFolderPath);
                        }

                        // Update DB References for files directly in this folder
                        $directFiles = Media::where('collection_name', $oldFolderPath)->get();
                        foreach($directFiles as $file) {
                             $file->collection_name = $newFolderPath;

                             // Calculate new file path by replacing old folder path with new one
                             $oldFilePath = $file->getPath(); // Gets current path (may include date subfolders)
                             // Replace the folder prefix in the path
                             if (str_starts_with($oldFilePath, $oldFolderPath . '/')) {
                                 $newFilePath = $newFolderPath . substr($oldFilePath, strlen($oldFolderPath));
                             } else {
                                 // Fallback: just put file directly in new folder
                                 $newFilePath = $newFolderPath . '/' . $file->file_name;
                             }

                             $customProperties = $file->custom_properties ?? [];
                             $customProperties['file_path'] = $newFilePath;
                             $file->custom_properties = $customProperties;
                             $file->save();
                        }

                        // 2. Files in sub-folders (Recursive update)
                        $subFiles = Media::where('collection_name', 'like', $oldFolderPath . '/%')->get();
                        foreach($subFiles as $file) {
                             // Update collection_name: e.g. 1/sub -> 2/1/sub
                             $newCollectionName = $newFolderPath . substr($file->collection_name, strlen($oldFolderPath));
                             $file->collection_name = $newCollectionName;

                             // Calculate new file path
                             $oldFilePath = $file->getPath();
                             if (str_starts_with($oldFilePath, $oldFolderPath . '/')) {
                                 $newFilePath = $newFolderPath . substr($oldFilePath, strlen($oldFolderPath));
                             } else {
                                 $newFilePath = $newCollectionName . '/' . $file->file_name;
                             }

                             $customProperties = $file->custom_properties ?? [];
                             $customProperties['file_path'] = $newFilePath;
                             $file->custom_properties = $customProperties;
                             $file->save();
                        }

                        $movedCount++;
                    }
                } catch (\Exception $e) {
                    \Log::error("Folder move failed PATH $id: " . $e->getMessage());
                }
            }
        }

        // Finalize
        session()->flash('success', 'Đã di chuyển thành công!');
        $this->loadFolders(); // FORCE REFRESH FOLDERS
        $this->selectedMedia = [];
        $this->dispatch('selection-cleared');
        $this->dropTarget = null; // Sync back to frontend if needed
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

                    // Create new path (direct in target folder, no date subdirectory)
                    $newPath = ($targetFolder ? $targetFolder . '/' : '') . $media->file_name;

                    // Collision check & Auto-rename
                    if (Storage::disk($disk)->exists($newPath)) {
                        $nameInfo = pathinfo($media->file_name);
                        $counter = 1;
                        while (Storage::disk($disk)->exists($newPath)) {
                            $newFileName = $nameInfo['filename'] . " ($counter)." . ($nameInfo['extension'] ?? '');
                            $newPath = ($targetFolder ? $targetFolder . '/' : '') . $newFileName;
                            $counter++;
                        }
                        $media->file_name = basename($newPath);
                    }

                    // Ensure target directory exists
                    if ($targetFolder && !Storage::disk($disk)->exists($targetFolder)) {
                        Storage::disk($disk)->makeDirectory($targetFolder);
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
        // Use onlyTrashed() if viewing trash, otherwise exclude trashed
        $query = $this->showTrash
            ? Media::onlyTrashed()->orderBy('deleted_at', 'desc')
            : Media::query()->orderBy('created_at', 'desc');

        // Filter by current folder/collection
        if ($this->showTrash) {
            // Trash view: filter by folder (same as normal view)
            if ($this->currentFolder) {
                $query->where('collection_name', $this->currentFolder);
            } else {
                // At root, show files in 'uploads', 'default', or empty collection
                $query->where(function($q) {
                    $q->whereIn('collection_name', ['uploads', 'default', ''])
                      ->orWhereNull('collection_name');
                });
            }
        } else {
            // Normal view: filter by folder
            if ($this->currentFolder) {
                $query->where('collection_name', $this->currentFolder);
            } else {
                // At root, only show files in 'uploads' or 'default' collection
                $disk = config('media.default_disk', 'public');
                $existingFolders = \Storage::disk($disk)->directories();

                if (!empty($existingFolders)) {
                    $query->where(function($q) use ($existingFolders) {
                        $q->whereIn('collection_name', ['uploads', 'default', ''])
                          ->orWhereNull('collection_name');
                    });
                }
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

    // === TRASH MANAGEMENT ===

    public function toggleTrash()
    {
        $this->showTrash = !$this->showTrash;
        $this->selectedMedia = [];
        $this->currentFolder = ''; // Reset to root when switching views
        $this->resetPage();
    }

    public function restoreItem($id, $type = 'file')
    {
        if ($type === 'folder') {
            $folder = MediaFolder::onlyTrashed()->find($id);
            if ($folder) {
                $this->restoreFolderRecursive($folder);
                session()->flash('success', 'Đã khôi phục folder thành công!');
            }
        } else {
            $media = Media::onlyTrashed()->find($id);
            if ($media) {
                $media->restore();
                session()->flash('success', 'Đã khôi phục file thành công!');
            }
        }
        $this->loadFolders();
    }

    /**
     * Recursively restore folder and all its contents.
     */
    protected function restoreFolderRecursive(MediaFolder $folder)
    {
        // Restore the folder first
        $folder->restore();

        // Restore all files in this folder
        Media::onlyTrashed()->where('collection_name', $folder->path)->restore();

        // Restore child folders recursively
        $trashedChildren = MediaFolder::onlyTrashed()->where('parent_id', $folder->id)->get();
        foreach ($trashedChildren as $child) {
            $this->restoreFolderRecursive($child);
        }
    }

    public function restoreSelected()
    {
        if (empty($this->selectedMedia)) {
            return;
        }

        $fileCount = 0;
        $folderCount = 0;

        foreach ($this->selectedMedia as $id) {
            if (is_string($id) && str_starts_with($id, 'folder:')) {
                $folderPath = trim(substr($id, 7), '/');
                $folder = MediaFolder::onlyTrashed()->where('path', $folderPath)->first();
                if ($folder) {
                    $this->restoreFolderRecursive($folder);
                    $folderCount++;
                }
            } elseif (is_numeric($id)) {
                $media = Media::onlyTrashed()->find($id);
                if ($media) {
                    $media->restore();
                    $fileCount++;
                }
            }
        }

        $this->selectedMedia = [];
        $this->loadFolders();

        $message = [];
        if ($fileCount > 0) $message[] = "{$fileCount} file";
        if ($folderCount > 0) $message[] = "{$folderCount} folder";
        session()->flash('success', 'Đã khôi phục ' . implode(' và ', $message) . '!');
    }

    public function forceDeleteItem($id, $type = 'file')
    {
        $disk = config('media.default_disk', 'public');

        if ($type === 'folder') {
            $folder = MediaFolder::onlyTrashed()->find($id);
            if ($folder) {
                $this->forceDeleteFolderRecursive($folder);
                session()->flash('success', 'Đã xóa vĩnh viễn folder!');
            }
        } else {
            $media = Media::onlyTrashed()->find($id);
            if ($media) {
                $path = $media->getPath();
                if (Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                }
                $media->forceDelete();
                session()->flash('success', 'Đã xóa vĩnh viễn file!');
            }
        }
        $this->loadFolders();
    }

    /**
     * Recursively force delete folder and all its contents.
     */
    protected function forceDeleteFolderRecursive(MediaFolder $folder)
    {
        $disk = config('media.default_disk', 'public');

        // Force delete child folders recursively
        $trashedChildren = MediaFolder::onlyTrashed()->where('parent_id', $folder->id)->get();
        foreach ($trashedChildren as $child) {
            $this->forceDeleteFolderRecursive($child);
        }

        // Force delete all files in this folder
        $trashedFiles = Media::onlyTrashed()->where('collection_name', $folder->path)->get();
        foreach ($trashedFiles as $file) {
            $path = $file->getPath();
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
            $file->forceDelete();
        }

        // Delete physical directory if exists
        if (Storage::disk($disk)->exists($folder->path)) {
            Storage::disk($disk)->deleteDirectory($folder->path);
        }

        // Force delete the folder record
        $folder->forceDelete();
    }

    public function forceDeleteSelected()
    {
        if (empty($this->selectedMedia)) {
            return;
        }

        $disk = config('media.default_disk', 'public');
        $fileCount = 0;
        $folderCount = 0;

        foreach ($this->selectedMedia as $id) {
            if (is_string($id) && str_starts_with($id, 'folder:')) {
                $folderPath = trim(substr($id, 7), '/');
                $folder = MediaFolder::onlyTrashed()->where('path', $folderPath)->first();
                if ($folder) {
                    $this->forceDeleteFolderRecursive($folder);
                    $folderCount++;
                }
            } elseif (is_numeric($id)) {
                $media = Media::onlyTrashed()->find($id);
                if ($media) {
                    $path = $media->getPath();
                    if (Storage::disk($disk)->exists($path)) {
                        Storage::disk($disk)->delete($path);
                    }
                    $media->forceDelete();
                    $fileCount++;
                }
            }
        }

        $this->selectedMedia = [];
        $this->loadFolders();

        $message = [];
        if ($fileCount > 0) $message[] = "{$fileCount} file";
        if ($folderCount > 0) $message[] = "{$folderCount} folder";
        session()->flash('success', 'Đã xóa vĩnh viễn ' . implode(' và ', $message) . '!');
    }

    public function emptyTrash()
    {
        $disk = config('media.default_disk', 'public');
        $fileCount = 0;
        $folderCount = 0;

        // Force delete all trashed files
        $trashedMedia = Media::onlyTrashed()->get();
        foreach ($trashedMedia as $media) {
            $path = $media->getPath();
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
            $media->forceDelete();
            $fileCount++;
        }

        // Force delete all trashed folders
        $trashedFolders = MediaFolder::onlyTrashed()->get();
        foreach ($trashedFolders as $folder) {
            if (Storage::disk($disk)->exists($folder->path)) {
                Storage::disk($disk)->deleteDirectory($folder->path);
            }
            $folder->forceDelete();
            $folderCount++;
        }

        $message = [];
        if ($fileCount > 0) $message[] = "{$fileCount} file";
        if ($folderCount > 0) $message[] = "{$folderCount} folder";
        session()->flash('success', 'Đã dọn sạch thùng rác (' . implode(' và ', $message) . ')!');
    }

    public function render()
    {
        $media = $this->getMediaQuery()->paginate($this->perPage);

        // Get all folders for move modal
        $disk = config('media.default_disk', 'public');
        $allFolders = Storage::disk($disk)->allDirectories();

        // Get trash folders if in trash view
        $trashFolders = $this->showTrash ? $this->loadTrashFolders() : [];

        return view('core/media::livewire.media-manager', [
            'mediaItems' => $media,
            'allFolders' => $allFolders,
            'editingImage' => $this->editingImage,
            'trashFolders' => $trashFolders,
        ]);
    }
}
