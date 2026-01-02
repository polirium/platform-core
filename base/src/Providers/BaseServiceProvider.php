<?php

namespace Polirium\Core\Base\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Polirium\Core\Base\Exceptions\Handler;
use Polirium\Core\Base\Helpers\Assets;
use Polirium\Core\Base\Helpers\BaseHelper;
use Polirium\Core\Base\Service\ModuleManager;
use Polirium\Core\Support\Providers\PoliriumBaseServiceProvider;

class BaseServiceProvider extends PoliriumBaseServiceProvider
{
    public function boot()
    {
        $this->setNamespace('core/base')
            ->loadConfigurations(['setting'])
            ->loadViews()
            ->loadTranslations()
            ->loadRoutes(['web', 'api', 'auth'])
            ->loadMigrations();

        $this->app->register(FortifyServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(LivewireServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        $this->app['events']->listen(RouteMatched::class, function () {
            $this->app->register(MenuServiceProvider::class);
        });

        Config::set('auth.providers.users.model', \Polirium\Core\Base\Http\Models\User::class);
        Config::set('livewire-powergrid.theme', \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class);

        $this->commands([
            \Polirium\Core\Base\Commands\InstallCommand::class,
            \Polirium\Core\Base\Commands\UserCreateCommand::class,
            \Polirium\Core\Base\Commands\ImportCountriesCommand::class,
            \Polirium\Core\Base\Commands\ModuleDependenciesCommand::class,
        ]);

        // Load modules using ModuleManager (if modules table exists)
        // Falls back to legacy registerModules() if not
        if (Schema::hasTable('modules')) {
            $this->app->make(ModuleManager::class)->loadActiveModules();
        } else {
            $this->registerModules();
        }
    }

    public function register()
    {
        BaseHelper::autoload(__DIR__ . '/../../helpers');

        $this->app->singleton(ExceptionHandler::class, Handler::class);

        $this->app->singleton('core:assets', function () {
            return new Assets();
        });

        $this->app->make('config')->set([
            'session.cookie' => 'polirium_session',
            'app.debug_blacklist' => [
                '_ENV' => [
                    'APP_KEY',
                    'ADMIN_DIR',
                    'DB_DATABASE',
                    'DB_USERNAME',
                    'DB_PASSWORD',
                    'REDIS_PASSWORD',
                    'MAIL_PASSWORD',
                    'PUSHER_APP_KEY',
                    'PUSHER_APP_SECRET',
                    'LOG_SLACK_WEBHOOK_URL',
                    'SFTP_HOST',
                    'SFTP_USERNAME',
                    'SFTP_PASSWORD',
                    'SFTP_ROOT',
                    'SFTP_ROOT_PUBLIC',
                    'MAIL_HOST_SES',
                    'MAIL_PORT_SES',
                    'MAIL_USERNAME_SES',
                    'MAIL_PASSWORD_SES',
                    'MAIL_HOST',
                    'MAIL_USERNAME',
                    'MAIL_PASSWORD',
                ],
                '_SERVER' => [
                    'APP_KEY',
                    'ADMIN_DIR',
                    'DB_DATABASE',
                    'DB_USERNAME',
                    'DB_PASSWORD',
                    'REDIS_PASSWORD',
                    'MAIL_PASSWORD',
                    'PUSHER_APP_KEY',
                    'PUSHER_APP_SECRET',
                    'LOG_SLACK_WEBHOOK_URL',
                    'SFTP_HOST',
                    'SFTP_USERNAME',
                    'SFTP_PASSWORD',
                    'SFTP_ROOT',
                    'SFTP_ROOT_PUBLIC',
                    'MAIL_HOST_SES',
                    'MAIL_PORT_SES',
                    'MAIL_USERNAME_SES',
                    'MAIL_PASSWORD_SES',
                    'MAIL_HOST',
                    'MAIL_USERNAME',
                    'MAIL_PASSWORD',
                ],
                '_POST' => [
                    'password',
                ],
            ],
        ]);
    }

    protected function setConfigurations()
    {
        Config::set('auth.providers.users.model', \Polirium\Core\Base\Http\Models\User::class);
        Config::set('livewire-powergrid.theme', \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class);
    }

    protected function registerModules()
    {
        $modulePath = base_path('platform/modules');

        if (! is_dir($modulePath)) {
            return;
        }

        $modules = array_diff(scandir($modulePath), ['.', '..']);

        // Đăng ký autoload trước khi register providers
        foreach ($modules as $module) {
            $moduleDir = $modulePath . '/' . $module;

            if (is_dir($moduleDir)) {
                $composerFile = $moduleDir . '/composer.json';
                if (file_exists($composerFile)) {
                    $composer = json_decode(file_get_contents($composerFile), true);

                    // Auto register module namespace
                    if (isset($composer['autoload']['psr-4'])) {
                        foreach ($composer['autoload']['psr-4'] as $namespace => $path) {
                            $realPath = $moduleDir . '/' . trim($path, '/');
                            // Sử dụng class-map để load trực tiếp
                            spl_autoload_register(function ($class) use ($namespace, $realPath) {
                                if (strpos($class, $namespace) === 0) {
                                    $relativeClass = substr($class, strlen($namespace));
                                    $file = $realPath . '/' . str_replace('\\', '/', $relativeClass) . '.php';
                                    if (file_exists($file)) {
                                        require_once $file;
                                    }
                                }
                            });
                        }
                    }
                }
            }
        }

        // Sau khi đã đăng ký autoload, tiến hành register providers
        foreach ($modules as $module) {
            $moduleDir = $modulePath . '/' . $module;

            if (is_dir($moduleDir)) {
                $composerFile = $moduleDir . '/composer.json';
                if (file_exists($composerFile)) {
                    $composer = json_decode(file_get_contents($composerFile), true);

                    // Register module providers
                    if (isset($composer['extra']['laravel']['providers'])) {
                        foreach ($composer['extra']['laravel']['providers'] as $provider) {
                            if (class_exists($provider)) {
                                $this->app->register($provider);
                            }
                        }
                    }

                    // Register module aliases
                    if (isset($composer['extra']['laravel']['aliases'])) {
                        foreach ($composer['extra']['laravel']['aliases'] as $alias => $class) {
                            if (class_exists($class)) {
                                $this->app->alias($class, $alias);
                            }
                        }
                    }
                }
            }
        }
    }

}
