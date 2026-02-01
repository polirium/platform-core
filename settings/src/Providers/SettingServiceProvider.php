<?php

namespace Polirium\Core\Settings\Providers;

use Polirium\Core\Base\Helpers\BaseHelper;
use Polirium\Core\Settings\Contracts\ContextSerializer as ContextSerializerContract;
use Polirium\Core\Settings\Contracts\KeyGenerator as KeyGeneratorContract;
use Polirium\Core\Settings\Contracts\Setting as SettingContract;
use Polirium\Core\Settings\Contracts\ValueSerializer as ValueSerializerContract;
use Polirium\Core\Settings\Drivers\Factory;
use Polirium\Core\Settings\Settings;
use Polirium\Core\Settings\Support\ContextSerializers\ContextSerializer;
use Polirium\Core\Settings\Support\ContextSerializers\DotNotationContextSerializer;
use Polirium\Core\Settings\Support\KeyGenerators\Md5KeyGenerator;
use Polirium\Core\Settings\Support\KeyGenerators\ReadableKeyGenerator;
use Polirium\Core\Settings\Support\SettingRegistry;
use Polirium\Core\Settings\Support\ValueSerializers\JsonValueSerializer;
use Polirium\Core\Settings\Support\ValueSerializers\ValueSerializer;
use Polirium\Core\Support\Providers\PoliriumBaseServiceProvider;

class SettingServiceProvider extends PoliriumBaseServiceProvider
{
    public function boot()
    {
        $this->setNamespace('core/settings')
            ->loadConfigurations(['config', 'livewire'])
            ->loadMigrations()
            ->loadRoutes(['web'])
            ->loadViews()
            ->loadTranslations()
            ->publishAssets();

        $this->registerDefaultSettings();

        try {
            $settings = $this->app['polirium:settings'];
            $lifetime = $settings->get('general.session_lifetime');
            if ($lifetime && is_numeric($lifetime) && $lifetime > 0) {
                config(['session.lifetime' => (int) $lifetime]);
            }
        } catch (\Throwable $th) {
            // Do nothing
        }
    }

    public function provides()
    {
        return [
            Settings::class,
            'SettingsFactory',
            'polirium:settings',
            'polirium:setting-registry',
        ];
    }

    public function register()
    {
        BaseHelper::autoload(__DIR__ . '/../../helpers');

        $this->app->singleton('SettingsFactory', function ($app) {
            return new Factory($app);
        });

        $this->registerContracts();
        $this->registerSettings();
        $this->registerSettingRegistry();
    }

    protected function registerContracts()
    {
        /**
         * 'context_serializer'
         * ContextSerializer::class
         * DotNotationContextSerializer::class
         */
        $this->app->bind(ContextSerializerContract::class, function ($app) {
            $contextSerializer = $app->make($app['config']['core.settings.config.context_serializer'] ?? ContextSerializer::class);

            return new $contextSerializer();
        });

        /**
         * 'key_generator'
         * Md5KeyGenerator::class
         * ReadableKeyGenerator::class
         */
        $this->app->bind(KeyGeneratorContract::class, function ($app) {
            $keyGenerator = $app->make($app['config']['core.settings.config.key_generator'] ?? Md5KeyGenerator::class);

            return new $keyGenerator();
        });

        /**
         * 'value_serializer'
         * ValueSerializer::class
         * JsonValueSerializer::class
         */
        $this->app->bind(ValueSerializerContract::class, function ($app) {
            $valueSerializer = $app->make($app['config']['core.settings.config.value_serializer'] ?? ValueSerializer::class);

            return new $valueSerializer();
        });

        $this->app->bind(SettingContract::class, function ($app) {
            return new $app['config']['core.settings.config.drivers.eloquent.model']();
        });
    }

    protected function registerSettings()
    {
        $this->app->singleton(Settings::class, function ($app) {
            $config = $app['config']['core.settings.config'];

            $keyGenerator = app(KeyGeneratorContract::class);
            $keyGenerator->setContextSerializer(
                $app->make(ContextSerializerContract::class)
            );

            $settings = new Settings(
                driver: $app['SettingsFactory']->driver(),
                keyGenerator: $keyGenerator,
                valueSerializer: $app->make(ValueSerializerContract::class),
            );
            $settings->useCacheKeyPrefix($config['cache_key_prefix'] ?? '');
            $settings->setCache($app['cache.store']);
            $settings->cacheDefaultValue($config['cache_default_value'] ?? false);
            if (config('app.key')) {
                $settings->setEncrypter($app['encrypter']);
            }

            $config['cache'] ? $settings->enableCache() : $settings->disableCache();
            $config['encryption'] ? $settings->enableEncryption() : $settings->disableEncryption();
            $config['teams'] ? $settings->enableTeams() : $settings->disableTeams();

            $settings->setTeamForeignKey($app['config']['settings.team_foreign_key'] ?? 'team_id');

            return $settings;
        });

        $this->app->singleton('polirium:settings', function ($app) {
            return $app[Settings::class];
        });
    }

    protected function registerSettingRegistry()
    {
        $this->app->singleton('polirium:setting-registry', function () {
            return new SettingRegistry();
        });
    }

    protected function registerDefaultSettings()
    {
        $registry = app('polirium:setting-registry');

        $registry->group('general', [
                'title' => 'core/base::general.general_settings',
                'icon' => 'settings',
                'description' => 'core/base::general.general_settings_description',
            ])
            ->add('title', [
                'type' => 'text',
                'label' => 'core/base::general.site_title',
                'description' => 'core/base::general.site_title_description',
                'default' => config('core.base.setting.title'),
                'required' => true,
                'validation' => ['required', 'string', 'max:255'],
            ])
            ->add('logo', [
                'type' => 'file',
                'label' => 'core/base::general.logo',
                'description' => 'core/base::general.logo_description',
                'default' => config('core.base.setting.logo'),
                'validation' => ['nullable', 'image', 'max:2048'],
                'attributes' => ['accept' => 'image/*'],
            ])
            ->add('favicon', [
                'type' => 'file',
                'label' => 'core/base::general.favicon',
                'description' => 'core/base::general.favicon_description',
                'default' => config('core.base.setting.favicon'),
                'validation' => ['nullable', 'image', 'max:1024'],
                'attributes' => ['accept' => 'image/*'],
            ])
            ->add('locale', [
                'type' => 'select',
                'label' => 'core/base::general.default_language',
                'description' => 'core/base::general.default_language_description',
                'default' => config('app.locale'),
                'options' => [
                    'vi' => 'Vietnamese',
                    'en' => 'English',
                ],
            ])
            ->add('session_lifetime', [
                'type' => 'number',
                'label' => 'core/base::general.session_lifetime',
                'description' => 'core/base::general.session_lifetime_description',
                'default' => config('session.lifetime'),
                'required' => true,
                'validation' => ['required', 'numeric', 'min:1'],
            ]);
    }

}
