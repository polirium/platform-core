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
use Polirium\Core\Settings\Support\ValueSerializers\JsonValueSerializer;
use Polirium\Core\Settings\Support\ValueSerializers\ValueSerializer;
use Polirium\Core\Support\Providers\PoliriumBaseServiceProvider;

class SettingServiceProvider extends PoliriumBaseServiceProvider
{
    public function boot()
    {
        $this->setNamespace('core/settings')
            ->loadConfigurations(['config'])
            ->loadMigrations();
    }

    public function provides()
    {
        return [
            Settings::class,
            'SettingsFactory',
            'polirium:settings',
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

}
