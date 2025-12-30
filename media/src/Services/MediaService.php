<?php

namespace Polirium\Core\Media\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Polirium\Core\Media\Contracts\MediaServiceInterface;
use Polirium\Core\Media\Models\Media;
use Polirium\Core\Media\Repositories\MediaRepository;
use Spatie\MediaLibrary\HasMedia;

class MediaService implements MediaServiceInterface
{
    /**
     * @var MediaRepository
     */
    protected $repository;

    /**
     * @var MediaUploadService
     */
    protected $uploadService;

    /**
     * MediaService constructor.
     *
     * @param MediaRepository $repository
     * @param MediaUploadService $uploadService
     */
    public function __construct(MediaRepository $repository, MediaUploadService $uploadService)
    {
        $this->repository = $repository;
        $this->uploadService = $uploadService;
    }

    /**
     * Upload a file.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return Media
     * @throws \Exception
     */
    public function upload(UploadedFile $file, array $options = []): Media
    {
        // Validate file
        $this->uploadService->validate($file, $options);

        // Extract metadata
        $metadata = $this->uploadService->extractMetadata($file);

        // Prepare options - extract only base collection name (first part of path)
        $rawCollection = $options['collection'] ?? config('media.default_collection', 'default');
        $collectionParts = explode('/', $rawCollection);
        $collection = $collectionParts[0] ?: 'uploads'; // First part only (e.g., 'uploads' not 'uploads/2025/12')

        $disk = $options['disk'] ?? config('media.default_disk', 'public');
        $name = $options['name'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Generate unique filename
        $filename = $this->uploadService->generateUniqueFilename($file, $options);

        // Create directory if not exists
        $directory = $collection . '/' . date('Y/m');
        $storagePath = \Storage::disk($disk)->path($directory);
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Store file
        $path = $file->storeAs($directory, $filename, $disk);

        // Create media record directly
        $media = new Media();
        $media->model_type = 'Polirium\Core\Media\Models\Media';
        $media->model_id = 0;
        $media->uuid = Str::uuid()->toString();
        $media->collection_name = $collection;
        $media->name = $name;
        $media->file_name = $filename;
        $media->mime_type = $file->getMimeType();
        $media->disk = $disk;
        $media->conversions_disk = $disk;
        $media->size = $file->getSize();
        $media->manipulations = [];
        $media->custom_properties = array_merge(
            $metadata,
            $options['custom_properties'] ?? [],
            ['file_path' => $path] // Store the actual file path
        );
        $media->generated_conversions = [];
        $media->responsive_images = [];
        $media->order_column = Media::max('order_column') + 1;
        $media->save();

        // Update model_id to self
        $media->model_id = $media->id;
        $media->save();

        return $media;
    }

    /**
     * Upload multiple files.
     *
     * @param array $files
     * @param array $options
     * @return array
     */
    public function uploadMultiple(array $files, array $options = []): array
    {
        $uploadedMedia = [];

        foreach ($files as $file) {
            try {
                $uploadedMedia[] = $this->upload($file, $options);
            } catch (\Exception $e) {
                // Log error but continue with other files
                \Log::error('Failed to upload file: ' . $e->getMessage());
            }
        }

        return $uploadedMedia;
    }

    /**
     * Upload from URL.
     *
     * @param string $url
     * @param array $options
     * @return Media
     * @throws \Exception
     */
    public function uploadFromUrl(string $url, array $options = []): Media
    {
        try {
            // Download file from URL
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                throw new \Exception('Failed to download file from URL');
            }

            // Get filename from URL or generate one
            $filename = $options['filename'] ?? basename(parse_url($url, PHP_URL_PATH));
            if (empty($filename)) {
                $filename = 'download_' . time();
            }

            // Save to temporary file
            $tempPath = sys_get_temp_dir() . '/' . $filename;
            file_put_contents($tempPath, $response->body());

            // Create UploadedFile instance
            $file = new UploadedFile(
                $tempPath,
                $filename,
                mime_content_type($tempPath),
                null,
                true
            );

            // Upload using normal upload method
            $media = $this->upload($file, $options);

            // Clean up temp file
            @unlink($tempPath);

            return $media;
        } catch (\Exception $e) {
            throw new \Exception('Failed to upload from URL: ' . $e->getMessage());
        }
    }

    /**
     * Upload from base64.
     *
     * @param string $base64
     * @param array $options
     * @return Media
     * @throws \Exception
     */
    public function uploadFromBase64(string $base64, array $options = []): Media
    {
        try {
            // Extract mime type and data
            if (preg_match('/^data:([^;]+);base64,(.+)$/', $base64, $matches)) {
                $mimeType = $matches[1];
                $data = base64_decode($matches[2]);
            } else {
                $data = base64_decode($base64);
                $mimeType = 'application/octet-stream';
            }

            // Generate filename
            $extension = $this->getExtensionFromMimeType($mimeType);
            $filename = ($options['filename'] ?? 'upload_' . time()) . '.' . $extension;

            // Save to temporary file
            $tempPath = sys_get_temp_dir() . '/' . $filename;
            file_put_contents($tempPath, $data);

            // Create UploadedFile instance
            $file = new UploadedFile(
                $tempPath,
                $filename,
                $mimeType,
                null,
                true
            );

            // Upload using normal upload method
            $media = $this->upload($file, $options);

            // Clean up temp file
            @unlink($tempPath);

            return $media;
        } catch (\Exception $e) {
            throw new \Exception('Failed to upload from base64: ' . $e->getMessage());
        }
    }

    /**
     * Get media by ID.
     *
     * @param int $id
     * @return Media|null
     */
    public function get(int $id): ?Media
    {
        return $this->repository->findById($id);
    }

    /**
     * Delete media.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Delete multiple media.
     *
     * @param array $ids
     * @return int
     */
    public function deleteMultiple(array $ids): int
    {
        return $this->repository->deleteMultiple($ids);
    }

    /**
     * Attach media to model.
     *
     * @param int $mediaId
     * @param Model $model
     * @param string $collection
     * @return bool
     */
    public function attachToModel(int $mediaId, Model $model, string $collection = 'default'): bool
    {
        if (!$model instanceof HasMedia) {
            throw new \Exception('Model must implement HasMedia interface');
        }

        $media = $this->get($mediaId);

        if (!$media) {
            return false;
        }

        try {
            // Copy media to new model
            $model->copyMedia($media->getPath())->toMediaCollection($collection);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to attach media to model: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Detach media from model.
     *
     * @param int $mediaId
     * @param Model $model
     * @return bool
     */
    public function detachFromModel(int $mediaId, Model $model): bool
    {
        if (!$model instanceof HasMedia) {
            throw new \Exception('Model must implement HasMedia interface');
        }

        $media = $this->get($mediaId);

        if (!$media) {
            return false;
        }

        try {
            $media->delete();
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to detach media from model: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update media metadata.
     *
     * @param int $mediaId
     * @param array $metadata
     * @return bool
     */
    public function updateMetadata(int $mediaId, array $metadata): bool
    {
        $media = $this->get($mediaId);

        if (!$media) {
            return false;
        }

        $updateData = [];

        if (isset($metadata['name'])) {
            $updateData['name'] = $metadata['name'];
        }

        if (isset($metadata['custom_properties'])) {
            $updateData['custom_properties'] = array_merge(
                $media->custom_properties ?? [],
                $metadata['custom_properties']
            );
        }

        return $this->repository->update($mediaId, $updateData);
    }

    /**
     * Generate conversions for media.
     *
     * @param int $mediaId
     * @return bool
     */
    public function generateConversions(int $mediaId): bool
    {
        $media = $this->get($mediaId);

        if (!$media || !$media->is_image) {
            return false;
        }

        try {
            // Conversions are automatically generated by Spatie Media Library
            // based on the model's registerMediaConversions method
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to generate conversions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Download media.
     *
     * @param int $id
     * @param string $conversion
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(int $id, string $conversion = '')
    {
        $media = $this->get($id);

        if (!$media) {
            abort(404, 'Media not found');
        }

        return $media->download($conversion);
    }

    /**
     * Get media by collection.
     *
     * @param string $collection
     * @return \Illuminate\Support\Collection
     */
    public function getByCollection(string $collection)
    {
        return $this->repository->findByCollection($collection);
    }

    /**
     * Search media.
     *
     * @param string $query
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function search(string $query, array $filters = [])
    {
        return $this->repository->search($query, $filters);
    }

    /**
     * Paginate media.
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    /**
     * Create a temporary model for media upload.
     *
     * @return HasMedia
     */
    protected function createTemporaryModel(): HasMedia
    {
        // Create a temporary model class that implements HasMedia
        return new class extends Model implements HasMedia {
            use \Spatie\MediaLibrary\InteractsWithMedia;

            protected $table = 'media';

            public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
            {
                $conversions = config('media.image_conversions', []);

                foreach ($conversions as $name => $settings) {
                    $conversion = $this->addMediaConversion($name);

                    if (isset($settings['width']) && isset($settings['height'])) {
                        $fit = $settings['fit'] ?? 'contain';

                        if ($fit === 'crop') {
                            $conversion->fit(\Spatie\Image\Enums\Fit::Crop, $settings['width'], $settings['height']);
                        } else {
                            $conversion->fit(\Spatie\Image\Enums\Fit::Contain, $settings['width'], $settings['height']);
                        }
                    }

                    if (config('media.optimize_images')) {
                        $conversion->optimize();
                    }
                }
            }
        };
    }

    /**
     * Get file extension from MIME type.
     *
     * @param string $mimeType
     * @return string
     */
    protected function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/plain' => 'txt',
            'video/mp4' => 'mp4',
            'audio/mpeg' => 'mp3',
        ];

        return $mimeMap[$mimeType] ?? 'bin';
    }
}
