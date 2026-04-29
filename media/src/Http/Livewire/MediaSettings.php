<?php

namespace Polirium\Core\Media\Http\Livewire;

use Illuminate\Support\Facades\File;
use Livewire\Component;

class MediaSettings extends Component
{
    // Upload Limits
    public int $maxFileSize = 10; // in MB
    public int $maxFilesPerUpload = 20;

    // Allowed Extensions (categorized)
    public array $allowedImages = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
    public array $allowedDocuments = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'];
    public array $allowedVideos = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
    public array $allowedAudio = ['mp3', 'wav', 'ogg', 'wma', 'aac'];
    public array $allowedArchives = ['zip', 'rar', '7z', 'tar', 'gz'];

    // Blocked Extensions (security - read-only display)
    public array $blockedExtensions = [];

    // Input for adding new extensions
    public string $newImageExt = '';
    public string $newDocumentExt = '';
    public string $newVideoExt = '';
    public string $newAudioExt = '';
    public string $newArchiveExt = '';

    public function mount()
    {
        // Load current settings from config or database
        $this->maxFileSize = (int) (config('media.max_file_size', 10485760) / 1024 / 1024);
        $this->maxFilesPerUpload = (int) config('media.max_files_per_upload', 20);
        $this->blockedExtensions = config('media.blocked_extensions', []);

        // Load allowed extensions
        $this->loadAllowedExtensions();
    }

    protected function loadAllowedExtensions(): void
    {
        $allowed = config('media.allowed_extensions', []);

        // Categorize by common types
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico', 'tiff', 'tif'];
        $docExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf'];
        $videoExts = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm', '3gp'];
        $audioExts = ['mp3', 'wav', 'ogg', 'wma', 'aac', 'flac', 'm4a'];
        $archiveExts = ['zip', 'rar', '7z', 'tar', 'gz', 'bz2'];

        $this->allowedImages = array_values(array_intersect($allowed, $imageExts));
        $this->allowedDocuments = array_values(array_intersect($allowed, $docExts));
        $this->allowedVideos = array_values(array_intersect($allowed, $videoExts));
        $this->allowedAudio = array_values(array_intersect($allowed, $audioExts));
        $this->allowedArchives = array_values(array_intersect($allowed, $archiveExts));
    }

    public function addExtension(string $type): void
    {
        $property = match ($type) {
            'image' => 'newImageExt',
            'document' => 'newDocumentExt',
            'video' => 'newVideoExt',
            'audio' => 'newAudioExt',
            'archive' => 'newArchiveExt',
            default => null,
        };

        $listProperty = match ($type) {
            'image' => 'allowedImages',
            'document' => 'allowedDocuments',
            'video' => 'allowedVideos',
            'audio' => 'allowedAudio',
            'archive' => 'allowedArchives',
            default => null,
        };

        if (! $property || ! $listProperty) {
            return;
        }

        $ext = strtolower(trim($this->$property, '. '));
        if (empty($ext)) {
            return;
        }

        // Check if blocked
        if (in_array($ext, $this->blockedExtensions)) {
            session()->flash('error', "Extension '$ext' bị chặn vì lý do bảo mật.");

            return;
        }

        // Check if already exists
        if (! in_array($ext, $this->$listProperty)) {
            $this->$listProperty[] = $ext;
        }

        $this->$property = '';
    }

    public function removeExtension(string $type, string $ext): void
    {
        $listProperty = match ($type) {
            'image' => 'allowedImages',
            'document' => 'allowedDocuments',
            'video' => 'allowedVideos',
            'audio' => 'allowedAudio',
            'archive' => 'allowedArchives',
            default => null,
        };

        if (! $listProperty) {
            return;
        }

        $this->$listProperty = array_values(array_diff($this->$listProperty, [$ext]));
    }

    public function save(): void
    {
        $this->authorize('settings.media');

        $this->validate([
            'maxFileSize' => 'required|integer|min:1|max:500',
            'maxFilesPerUpload' => 'required|integer|min:1|max:100',
        ]);

        // Combine all allowed extensions
        $allExtensions = array_merge(
            $this->allowedImages,
            $this->allowedDocuments,
            $this->allowedVideos,
            $this->allowedAudio,
            $this->allowedArchives
        );

        // Update .env file (or use database settings)
        $this->updateEnvValue('MEDIA_MAX_FILE_SIZE', $this->maxFileSize * 1024 * 1024);
        $this->updateEnvValue('MEDIA_MAX_FILES_PER_UPLOAD', $this->maxFilesPerUpload);

        // Save allowed extensions to database settings
        setting([
            'media.allowed_extensions' => $allExtensions,
            'media.allowed_images' => $this->allowedImages,
            'media.allowed_documents' => $this->allowedDocuments,
            'media.allowed_videos' => $this->allowedVideos,
            'media.allowed_audio' => $this->allowedAudio,
            'media.allowed_archives' => $this->allowedArchives,
        ]);

        // Clear config cache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        session()->flash('success', 'Đã lưu cài đặt Media thành công!');
    }

    protected function updateEnvValue(string $key, $value): void
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            return;
        }

        $content = File::get($envPath);

        // Check if key exists
        if (preg_match("/^{$key}=/m", $content)) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}";
        }

        File::put($envPath, $content);
    }

    public function render()
    {
        return view('core/media::livewire.media-settings');
    }
}
