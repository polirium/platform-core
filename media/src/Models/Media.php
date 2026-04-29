<?php

namespace Polirium\Core\Media\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use SoftDeletes;
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'manipulations' => 'array',
        'custom_properties' => 'array',
        'generated_conversions' => 'array',
        'responsive_images' => 'array',
    ];

    /**
     * Scope a query to only include images.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    /**
     * Scope a query to only include documents.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Scope a query to only include videos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    /**
     * Scope a query to only include audio files.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAudio($query)
    {
        return $query->where('mime_type', 'like', 'audio/%');
    }

    /**
     * Check if the media is an image.
     *
     * @return bool
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if the media is a video.
     *
     * @return bool
     */
    public function getIsVideoAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    /**
     * Check if the media is a document.
     *
     * @return bool
     */
    public function getIsDocumentAttribute(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ]);
    }

    /**
     * Check if the media is audio.
     *
     * @return bool
     */
    public function getIsAudioAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'audio/');
    }

    /**
     * Get formatted file size.
     *
     * @return string
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the public URL for the media.
     *
     * @param string $conversion
     * @return string
     */
    public function getPublicUrl(string $conversion = ''): string
    {
        return $this->getUrl($conversion);
    }

    /**
     * Get the secure URL for the media (through controller, not direct storage).
     *
     * @return string
     */
    public function getSecureUrl(): string
    {
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);

        return route('media.serve', [
            'slug' => $this->uuid,
            'extension' => $extension,
        ]);
    }

    /**
     * Get the secure download URL for the media.
     *
     * @return string
     */
    public function getSecureDownloadUrl(): string
    {
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);

        return route('media.download.secure', [
            'slug' => $this->uuid,
            'extension' => $extension,
        ]);
    }

    /**
     * Get the URL for the media.
     *
     * @param string $conversionName
     * @return string
     */
    public function getUrl(string $conversionName = ''): string
    {
        $path = $this->getPath($conversionName);
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk($this->disk);

        return $storage->url($path);
    }

    /**
     * Get the path for the media.
     *
     * @param string $conversionName
     * @return string
     */
    public function getPath(string $conversionName = ''): string
    {
        // Check if we have a stored path
        if (! empty($this->custom_properties['file_path'])) {
            return $this->custom_properties['file_path'];
        }

        // Build path: collection/year/month/filename
        $directory = $this->collection_name . '/' . $this->created_at->format('Y/m');

        return $directory . '/' . $this->file_name;
    }

    /**
     * Get a temporary URL for the media.
     *
     * @param \DateTimeInterface|null $expiration
     * @param string $conversionName
     * @param array $options
     * @return string
     */
    public function getTemporaryUrl(?\DateTimeInterface $expiration = null, string $conversionName = '', array $options = []): string
    {
        $path = $this->getPath($conversionName);

        if (Storage::disk($this->disk)->exists($path)) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
            $storage = Storage::disk($this->disk);

            return $storage->temporaryUrl(
                $path,
                $expiration ?? now()->addHour(),
                $options
            );
        }

        return $this->getUrl($conversionName);
    }

    /**
     * Download the media file.
     *
     * @param string $conversion
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(string $conversion = '')
    {
        $path = $this->getPath($conversion);
        $filename = $conversion ? "{$this->file_name}_{$conversion}.{$this->extension}" : $this->file_name;

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk($this->disk);

        return $storage->download($path, $filename);
    }

    /**
     * Get the icon for the media type.
     *
     * @return string
     */
    public function getIconAttribute(): string
    {
        if ($this->is_image) {
            return 'photo';
        }

        if ($this->is_video) {
            return 'video';
        }

        if ($this->is_audio) {
            return 'music';
        }

        if ($this->is_document) {
            return match ($this->extension) {
                'pdf' => 'file-pdf',
                'doc', 'docx' => 'file-word',
                'xls', 'xlsx' => 'file-excel',
                'ppt', 'pptx' => 'file-powerpoint',
                default => 'file-text',
            };
        }

        return 'file';
    }

    /**
     * Get the extension without dot.
     *
     * @return string
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Get the filename without extension.
     *
     * @return string
     */
    public function getFilenameWithoutExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_FILENAME);
    }
}
