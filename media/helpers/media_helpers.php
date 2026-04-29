<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Polirium\Core\Media\Models\Media;

if (! function_exists('media_upload')) {
    /**
     * Upload a file to media library.
     *
     * @param UploadedFile $file
     * @param string $collection
     * @param Model|null $model
     * @param array $options
     * @return Media
     */
    function media_upload(UploadedFile $file, string $collection = 'default', ?Model $model = null, array $options = []): Media
    {
        $options['collection'] = $collection;

        $mediaService = app(\Polirium\Core\Media\Services\MediaService::class);
        $media = $mediaService->upload($file, $options);

        if ($model && $model instanceof \Spatie\MediaLibrary\HasMedia) {
            $mediaService->attachToModel($media->id, $model, $collection);
        }

        return $media;
    }
}

if (! function_exists('media_url')) {
    /**
     * Get the URL of a media file.
     *
     * @param Media|int $media
     * @param string $conversion
     * @return string
     */
    function media_url($media, string $conversion = ''): string
    {
        if (is_int($media)) {
            $media = app(\Polirium\Core\Media\Services\MediaService::class)->get($media);
        }

        if (! $media) {
            return '';
        }

        return $media->getUrl($conversion);
    }
}

if (! function_exists('media_get')) {
    /**
     * Get media by ID.
     *
     * @param int $id
     * @return Media|null
     */
    function media_get(int $id): ?Media
    {
        return app(\Polirium\Core\Media\Services\MediaService::class)->get($id);
    }
}

if (! function_exists('media_delete')) {
    /**
     * Delete media by ID.
     *
     * @param int $id
     * @return bool
     */
    function media_delete(int $id): bool
    {
        return app(\Polirium\Core\Media\Services\MediaService::class)->delete($id);
    }
}

if (! function_exists('media_of')) {
    /**
     * Get all media of a model.
     *
     * @param Model $model
     * @param string $collection
     * @return \Illuminate\Support\Collection
     */
    function media_of(Model $model, string $collection = 'default')
    {
        if (! $model instanceof \Spatie\MediaLibrary\HasMedia) {
            return collect([]);
        }

        return $model->getMedia($collection);
    }
}

if (! function_exists('media_download')) {
    /**
     * Download a media file.
     *
     * @param Media|int $media
     * @param string $conversion
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    function media_download($media, string $conversion = '')
    {
        if (is_int($media)) {
            $media = app(\Polirium\Core\Media\Services\MediaService::class)->get($media);
        }

        if (! $media) {
            abort(404, 'Media not found');
        }

        return $media->download($conversion);
    }
}

if (! function_exists('media_is_image')) {
    /**
     * Check if media is an image.
     *
     * @param Media|int $media
     * @return bool
     */
    function media_is_image($media): bool
    {
        if (is_int($media)) {
            $media = app(\Polirium\Core\Media\Services\MediaService::class)->get($media);
        }

        if (! $media) {
            return false;
        }

        return $media->is_image;
    }
}

if (! function_exists('media_thumbnail')) {
    /**
     * Get thumbnail URL of media.
     *
     * @param Media|int $media
     * @param string $conversion
     * @return string
     */
    function media_thumbnail($media, string $conversion = 'thumb'): string
    {
        return media_url($media, $conversion);
    }
}

if (! function_exists('media_upload_from_url')) {
    /**
     * Upload media from URL.
     *
     * @param string $url
     * @param array $options
     * @return Media
     */
    function media_upload_from_url(string $url, array $options = []): Media
    {
        return app(\Polirium\Core\Media\Services\MediaService::class)->uploadFromUrl($url, $options);
    }
}

if (! function_exists('media_upload_from_base64')) {
    /**
     * Upload media from base64 string.
     *
     * @param string $base64
     * @param array $options
     * @return Media
     */
    function media_upload_from_base64(string $base64, array $options = []): Media
    {
        return app(\Polirium\Core\Media\Services\MediaService::class)->uploadFromBase64($base64, $options);
    }
}

if (! function_exists('media_search')) {
    /**
     * Search media.
     *
     * @param string $query
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    function media_search(string $query, array $filters = [])
    {
        return app(\Polirium\Core\Media\Services\MediaService::class)->search($query, $filters);
    }
}

if (! function_exists('media_paginate')) {
    /**
     * Paginate media.
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    function media_paginate(int $perPage = 15, array $filters = [])
    {
        return app(\Polirium\Core\Media\Services\MediaService::class)->paginate($perPage, $filters);
    }
}

if (! function_exists('media_temporary_url')) {
    /**
     * Get temporary URL for media.
     *
     * @param Media|int $media
     * @param \DateTimeInterface|null $expiration
     * @param string $conversion
     * @return string
     */
    function media_temporary_url($media, ?\DateTimeInterface $expiration = null, string $conversion = ''): string
    {
        if (is_int($media)) {
            $media = app(\Polirium\Core\Media\Services\MediaService::class)->get($media);
        }

        if (! $media) {
            return '';
        }

        $expiration = $expiration ?? now()->addHour();

        return $media->getTemporaryUrl($expiration, $conversion);
    }
}
