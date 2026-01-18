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
            ->loadTranslations()
            ->loadMigrations();

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Polirium\Core\Media\Console\Commands\CleanupOrphanedMedia::class,
                \Polirium\Core\Media\Console\Commands\SyncMediaFolders::class,
            ]);
        }

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

        // Register assets
        if (file_exists(__DIR__ . '/../../config/assets.php')) {
            $assets = require __DIR__ . '/../../config/assets.php';

            if (!empty($assets['css'])) {
                \Polirium\Core\UI\Facades\Assets::addOptionalCss($assets['css']);
            }

            if (!empty($assets['js'])) {
                \Polirium\Core\UI\Facades\Assets::addOptionalJs($assets['js']);
            }
        }

        // Register Media Settings to Settings Sidebar
        $this->registerSettings();
    }

    /**
     * Register Media settings to the global Settings page.
     */
    protected function registerSettings(): void
    {
        if (!class_exists(\Polirium\Core\Settings\Facades\SettingRegistry::class)) {
            return;
        }

        \Polirium\Core\Settings\Facades\SettingRegistry::group('media', [
                'title' => 'Cài đặt Media',
                'icon' => 'photo',
                'description' => 'Cấu hình upload file và quản lý media'
            ])
            ->add('max_file_size', [
                'type' => 'number',
                'label' => 'Dung lượng tối đa (MB)',
                'description' => 'Kích thước file upload tối đa (megabytes)',
                'default' => 10,
                'required' => true,
                'validation' => ['required', 'integer', 'min:1', 'max:500'],
                'attributes' => ['min' => 1, 'max' => 500]
            ])
            ->add('max_files_per_upload', [
                'type' => 'number',
                'label' => 'Số file tối đa mỗi lần',
                'description' => 'Số lượng file tối đa cho phép upload một lần',
                'default' => 20,
                'required' => true,
                'validation' => ['required', 'integer', 'min:1', 'max:100'],
                'attributes' => ['min' => 1, 'max' => 100]
            ])
            ->add('allowed_image_extensions', [
                'type' => 'text',
                'label' => 'Định dạng hình ảnh',
                'description' => 'Các định dạng hình ảnh được phép (phân cách bằng dấu phẩy)',
                'default' => 'jpg,jpeg,png,gif,webp,svg,bmp,ico',
                'validation' => ['required', 'string']
            ])
            ->add('allowed_document_extensions', [
                'type' => 'text',
                'label' => 'Định dạng tài liệu',
                'description' => 'Các định dạng tài liệu được phép (phân cách bằng dấu phẩy)',
                'default' => 'pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv',
                'validation' => ['required', 'string']
            ])
            ->add('allowed_video_extensions', [
                'type' => 'text',
                'label' => 'Định dạng video',
                'description' => 'Các định dạng video được phép (phân cách bằng dấu phẩy)',
                'default' => 'mp4,avi,mov,wmv,flv,mkv,webm',
                'validation' => ['required', 'string']
            ])
            ->add('allowed_audio_extensions', [
                'type' => 'text',
                'label' => 'Định dạng audio',
                'description' => 'Các định dạng audio được phép (phân cách bằng dấu phẩy)',
                'default' => 'mp3,wav,ogg,wma,aac',
                'validation' => ['required', 'string']
            ])
            ->add('optimize_images', [
                'type' => 'checkbox',
                'label' => 'Tối ưu hình ảnh',
                'description' => 'Tự động nén và tối ưu hình ảnh khi upload',
                'default' => true
            ])
            ->add('generate_thumbnails', [
                'type' => 'checkbox',
                'label' => 'Tạo thumbnail',
                'description' => 'Tự động tạo thumbnail cho hình ảnh',
                'default' => true
            ]);
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
