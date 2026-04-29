<?php

namespace Polirium\Core\Media\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MediaUploadService
{
    /**
     * Validate uploaded file.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return bool
     * @throws \Exception
     */
    public function validate(UploadedFile $file, array $options = []): bool
    {
        $maxSize = $options['max_size'] ?? config('media.max_file_size');
        $allowedExtensions = $options['allowed_extensions'] ?? config('media.allowed_extensions');

        $validator = Validator::make(
            ['file' => $file],
            [
                'file' => [
                    'required',
                    'file',
                    'max:' . ($maxSize / 1024), // Convert to KB
                    function ($attribute, $value, $fail) use ($allowedExtensions) {
                        $extension = $value->getClientOriginalExtension();
                        if (! in_array(strtolower($extension), $allowedExtensions)) {
                            $fail("The file extension {$extension} is not allowed.");
                        }
                    },
                ],
            ]
        );

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        return true;
    }

    /**
     * Sanitize filename.
     *
     * @param string $filename
     * @return string
     */
    public function sanitizeFilename(string $filename): string
    {
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);

        // Remove leading/trailing underscores
        $filename = trim($filename, '_');

        return $filename;
    }

    /**
     * Generate unique filename.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return string
     */
    public function generateUniqueFilename(UploadedFile $file, array $options = []): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        if (isset($options['preserve_original_name']) && $options['preserve_original_name']) {
            $filename = $this->sanitizeFilename($originalName);
        } else {
            $filename = Str::slug($originalName) . '_' . time() . '_' . Str::random(8);
        }

        return $filename . '.' . $extension;
    }

    /**
     * Process image (resize, optimize).
     *
     * @param UploadedFile $file
     * @param array $options
     * @return UploadedFile
     */
    public function processImage(UploadedFile $file, array $options = []): UploadedFile
    {
        if (! $this->isImage($file)) {
            return $file;
        }

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            // Resize if max dimensions specified
            if (isset($options['max_width']) || isset($options['max_height'])) {
                $maxWidth = $options['max_width'] ?? null;
                $maxHeight = $options['max_height'] ?? null;

                if ($maxWidth && $image->width() > $maxWidth) {
                    $image->scale(width: $maxWidth);
                }

                if ($maxHeight && $image->height() > $maxHeight) {
                    $image->scale(height: $maxHeight);
                }
            }

            // Save processed image
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
            $image->save($tempPath);

            // Create new UploadedFile instance
            return new UploadedFile(
                $tempPath,
                $file->getClientOriginalName(),
                $file->getMimeType(),
                null,
                true
            );
        } catch (\Exception $e) {
            // If processing fails, return original file
            return $file;
        }
    }

    /**
     * Check if file is an image.
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    /**
     * Check if file is a video.
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function isVideo(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'video/');
    }

    /**
     * Check if file is a document.
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function isDocument(UploadedFile $file): bool
    {
        $documentMimes = config('media.mime_types.documents', []);

        return in_array($file->getMimeType(), $documentMimes);
    }

    /**
     * Get file type category.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function getFileType(UploadedFile $file): string
    {
        if ($this->isImage($file)) {
            return 'image';
        }

        if ($this->isVideo($file)) {
            return 'video';
        }

        if ($this->isDocument($file)) {
            return 'document';
        }

        if (str_starts_with($file->getMimeType(), 'audio/')) {
            return 'audio';
        }

        return 'other';
    }

    /**
     * Extract metadata from file.
     *
     * @param UploadedFile $file
     * @return array
     */
    public function extractMetadata(UploadedFile $file): array
    {
        $metadata = [
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'type' => $this->getFileType($file),
        ];

        // Extract image metadata
        if ($this->isImage($file)) {
            try {
                $imageSize = getimagesize($file->getRealPath());
                if ($imageSize) {
                    $metadata['width'] = $imageSize[0];
                    $metadata['height'] = $imageSize[1];
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return $metadata;
    }

    /**
     * Handle chunked upload.
     *
     * @param array $chunk
     * @param array $options
     * @return array
     */
    public function handleChunkedUpload(array $chunk, array $options = []): array
    {
        $chunkIndex = $chunk['index'] ?? 0;
        $totalChunks = $chunk['total'] ?? 1;
        $file = $chunk['file'] ?? null;
        $identifier = $chunk['identifier'] ?? Str::random(32);

        if (! $file) {
            throw new \Exception('No file provided in chunk');
        }

        $tempDir = storage_path('app/temp/chunks/' . $identifier);

        if (! file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Save chunk
        $chunkPath = $tempDir . '/chunk_' . $chunkIndex;
        move_uploaded_file($file->getRealPath(), $chunkPath);

        // Check if all chunks are uploaded
        $uploadedChunks = count(glob($tempDir . '/chunk_*'));

        if ($uploadedChunks === $totalChunks) {
            // Merge chunks
            $finalPath = $tempDir . '/' . ($options['filename'] ?? 'merged_file');
            $finalFile = fopen($finalPath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkFile = fopen($tempDir . '/chunk_' . $i, 'rb');
                stream_copy_to_stream($chunkFile, $finalFile);
                fclose($chunkFile);
                unlink($tempDir . '/chunk_' . $i);
            }

            fclose($finalFile);

            return [
                'completed' => true,
                'path' => $finalPath,
                'identifier' => $identifier,
            ];
        }

        return [
            'completed' => false,
            'uploaded' => $uploadedChunks,
            'total' => $totalChunks,
            'identifier' => $identifier,
        ];
    }
}
