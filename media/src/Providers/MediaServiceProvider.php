<?php

namespace Polirium\Core\Media\Providers;

use Polirium\Core\Media\Contracts\MediaRepositoryInterface;
use Polirium\Core\Media\Contracts\MediaServiceInterface;
use Polirium\Core\Media\Models\Media;
use Polirium\Core\Media\Repositories\MediaRepository;
use Polirium\Core\Media\Services\MediaService;
use Polirium\Core\Media\Services\MediaUploadService;
use Polirium\Core\Support\Providers\PoliriumBaseServiceProvider;

class MediaServiceProvider extends PoliriumBaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setNamespace('core/media')
            ->loadConfigurations(['media', 'menu'])
            ->loadRoutes(['web', 'api'])
            ->loadViews()
            ->loadMigrations();

        // Load helpers
        if (file_exists(__DIR__ . '/../../helpers/media_helpers.php')) {
            require_once __DIR__ . '/../../helpers/media_helpers.php';
        }

        // Publish config
        $this->publishes([
            __DIR__ . '/../../config/media.php' => config_path('media.php'),
        ], 'media-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
        ], 'media-migrations');

        // Override Spatie Media Library model
        if (config('media-library.media_model') === \Spatie\MediaLibrary\MediaCollections\Models\Media::class) {
            config(['media-library.media_model' => Media::class]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/media.php',
            'media'
        );

        // Register repository
        $this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);

        // Register services
        $this->app->singleton(MediaUploadService::class, function ($app) {
            return new MediaUploadService();
        });

        $this->app->singleton(MediaServiceInterface::class, function ($app) {
            return new MediaService(
                $app->make(MediaRepository::class),
                $app->make(MediaUploadService::class)
            );
        });

        // Register facade accessor
        $this->app->singleton('media.service', function ($app) {
            return $app->make(MediaServiceInterface::class);
        });

        // Register alias
        $this->app->alias('media.service', MediaService::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            MediaServiceInterface::class,
            MediaRepositoryInterface::class,
            MediaUploadService::class,
            'media.service',
        ];
    }
}
