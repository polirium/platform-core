<?php

namespace Polirium\Core\Media\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Polirium\Core\Media\Models\Media;

interface MediaServiceInterface
{
    /**
     * Upload a file.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return Media
     */
    public function upload(UploadedFile $file, array $options = []): Media;

    /**
     * Upload multiple files.
     *
     * @param array $files
     * @param array $options
     * @return array
     */
    public function uploadMultiple(array $files, array $options = []): array;

    /**
     * Upload from URL.
     *
     * @param string $url
     * @param array $options
     * @return Media
     */
    public function uploadFromUrl(string $url, array $options = []): Media;

    /**
     * Upload from base64.
     *
     * @param string $base64
     * @param array $options
     * @return Media
     */
    public function uploadFromBase64(string $base64, array $options = []): Media;

    /**
     * Get media by ID.
     *
     * @param int $id
     * @return Media|null
     */
    public function get(int $id): ?Media;

    /**
     * Delete media.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Delete multiple media.
     *
     * @param array $ids
     * @return int
     */
    public function deleteMultiple(array $ids): int;

    /**
     * Attach media to model.
     *
     * @param int $mediaId
     * @param Model $model
     * @param string $collection
     * @return bool
     */
    public function attachToModel(int $mediaId, Model $model, string $collection = 'default'): bool;

    /**
     * Detach media from model.
     *
     * @param int $mediaId
     * @param Model $model
     * @return bool
     */
    public function detachFromModel(int $mediaId, Model $model): bool;

    /**
     * Update media metadata.
     *
     * @param int $mediaId
     * @param array $metadata
     * @return bool
     */
    public function updateMetadata(int $mediaId, array $metadata): bool;

    /**
     * Generate conversions for media.
     *
     * @param int $mediaId
     * @return bool
     */
    public function generateConversions(int $mediaId): bool;
}
