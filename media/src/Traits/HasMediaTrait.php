<?php

namespace Polirium\Core\Media\Traits;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\InteractsWithMedia;

trait HasMediaTrait
{
    use InteractsWithMedia;

    /**
     * Upload media to this model.
     *
     * @param UploadedFile $file
     * @param string $collection
     * @param array $options
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     */
    public function uploadMedia(UploadedFile $file, string $collection = 'default', array $options = [])
    {
        $mediaItem = $this->addMedia($file);

        if (isset($options['name'])) {
            $mediaItem->usingName($options['name']);
        }

        if (isset($options['custom_properties'])) {
            $mediaItem->withCustomProperties($options['custom_properties']);
        }

        if (isset($options['filename'])) {
            $mediaItem->usingFileName($options['filename']);
        }

        return $mediaItem->toMediaCollection($collection);
    }

    /**
     * Upload multiple media files to this model.
     *
     * @param array $files
     * @param string $collection
     * @param array $options
     * @return array
     */
    public function uploadMultipleMedia(array $files, string $collection = 'default', array $options = []): array
    {
        $uploadedMedia = [];

        foreach ($files as $file) {
            try {
                $uploadedMedia[] = $this->uploadMedia($file, $collection, $options);
            } catch (\Exception $e) {
                \Log::error('Failed to upload media: ' . $e->getMessage());
            }
        }

        return $uploadedMedia;
    }

    /**
     * Get the first media URL from a collection.
     *
     * @param string $collection
     * @param string $conversion
     * @return string
     */
    public function getFirstMediaUrl(string $collection = 'default', string $conversion = ''): string
    {
        $media = $this->getFirstMedia($collection);

        if (! $media) {
            return '';
        }

        return $media->getUrl($conversion);
    }

    /**
     * Get all media URLs from a collection.
     *
     * @param string $collection
     * @param string $conversion
     * @return array
     */
    public function getAllMediaUrls(string $collection = 'default', string $conversion = ''): array
    {
        return $this->getMedia($collection)->map(function ($media) use ($conversion) {
            return $media->getUrl($conversion);
        })->toArray();
    }

    /**
     * Delete all media from a collection.
     *
     * @param string $collection
     * @return void
     */
    public function deleteAllMedia(string $collection = ''): void
    {
        if (empty($collection)) {
            $this->clearMediaCollection();
        } else {
            $this->clearMediaCollection($collection);
        }
    }

    /**
     * Check if model has media in a collection.
     *
     * @param string $collection
     * @return bool
     */
    public function hasMedia(string $collection = ''): bool
    {
        if (empty($collection)) {
            return $this->media()->count() > 0;
        }

        return $this->getMedia($collection)->count() > 0;
    }

    /**
     * Get media count for a collection.
     *
     * @param string $collection
     * @return int
     */
    public function getMediaCount(string $collection = ''): int
    {
        if (empty($collection)) {
            return $this->media()->count();
        }

        return $this->getMedia($collection)->count();
    }

    /**
     * Replace media in a collection.
     *
     * @param UploadedFile $file
     * @param string $collection
     * @param array $options
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     */
    public function replaceMedia(UploadedFile $file, string $collection = 'default', array $options = [])
    {
        // Delete existing media in collection
        $this->clearMediaCollection($collection);

        // Upload new media
        return $this->uploadMedia($file, $collection, $options);
    }

    /**
     * Get the first media from a collection.
     *
     * @param string $collection
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media|null
     */
    public function getFirstMediaItem(string $collection = 'default')
    {
        return $this->getFirstMedia($collection);
    }

    /**
     * Upload media from URL.
     *
     * @param string $url
     * @param string $collection
     * @param array $options
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     */
    public function uploadMediaFromUrl(string $url, string $collection = 'default', array $options = [])
    {
        $mediaItem = $this->addMediaFromUrl($url);

        if (isset($options['name'])) {
            $mediaItem->usingName($options['name']);
        }

        if (isset($options['custom_properties'])) {
            $mediaItem->withCustomProperties($options['custom_properties']);
        }

        return $mediaItem->toMediaCollection($collection);
    }

    /**
     * Register media conversions.
     * Override this method in your model to define custom conversions.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media|null $media
     * @return void
     */
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
}
