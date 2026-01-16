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
use Polirium\Core\Settings\Facades\SettingRegistry;
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
            ->loadMigrations()
            ->publishAssets();

        $this->app->register(FortifyServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(LivewireServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        $this->app['events']->listen(RouteMatched::class, function () {
            $this->app->register(MenuServiceProvider::class);
        });

        Config::set('auth.providers.users.model', \Polirium\Core\Base\Http\Models\User::class);
        Config::set('polirium-datatable.theme', \Polirium\Datatable\Themes\Tabler::class);

        $this->commands([
            \Polirium\Core\Base\Commands\InstallCommand::class,
            \Polirium\Core\Base\Commands\UserCreateCommand::class,
            \Polirium\Core\Base\Commands\ImportCountriesCommand::class,
            \Polirium\Core\Base\Commands\ModuleDependenciesCommand::class,
            \Polirium\Core\Base\Commands\SampleDataCommand::class,
            \Polirium\Core\Base\Commands\AssetsPublishCommand::class,
        ]);

        // Register base settings
        $this->registerSettings();

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

        $this->app->singleton(\Polirium\Core\Base\Services\PageTitle::class);

        // Register Dashboard Widget Registry
        $this->app->singleton('dashboard.widgets', function () {
            return new \Polirium\Core\Base\Services\Dashboard\WidgetRegistry();
        });

        // Auto-discover and register widgets
        $this->app->booted(function () {
            $registry = $this->app->make('dashboard.widgets');
            $discovery = new \Polirium\Core\Base\Services\Dashboard\WidgetDiscoveryService($registry);
            $discovery->discover();
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
        Config::set('polirium-datatable.theme', \Polirium\Datatable\Themes\Tabler::class);
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

    /**
     * Register base module settings
     */
    protected function registerSettings(): void
    {
        SettingRegistry::group('email', [
            'title' => 'core/base::general.email_configuration',
            'icon' => 'mail',
            'description' => 'core/base::general.email_configuration_description',
        ])
        ->add('smtp_host', [
            'type' => 'text',
            'label' => 'core/base::general.smtp_host',
            'description' => 'core/base::general.smtp_host_description',
            'default' => config('mail.host', 'smtp.mailgun.org'),
            'required' => true,
            'validation' => ['required', 'string', 'max:255'],
        ])
        ->add('smtp_port', [
            'type' => 'number',
            'label' => 'core/base::general.smtp_port',
            'description' => 'core/base::general.smtp_port_description',
            'default' => config('mail.port', 587),
            'required' => true,
            'validation' => ['required', 'integer', 'min:1', 'max:65535'],
            'attributes' => ['min' => 1, 'max' => 65535],
        ])
        ->add('smtp_username', [
            'type' => 'text',
            'label' => 'core/base::general.smtp_username',
            'description' => 'core/base::general.smtp_username_description',
            'default' => config('mail.username'),
            'validation' => ['nullable', 'string', 'max:255'],
        ])
        ->add('smtp_password', [
            'type' => 'password',
            'label' => 'core/base::general.smtp_password',
            'description' => 'core/base::general.smtp_password_description',
            'validation' => ['nullable', 'string', 'max:255'],
        ])
        ->add('smtp_encryption', [
            'type' => 'select',
            'label' => 'core/base::general.smtp_encryption',
            'description' => 'core/base::general.smtp_encryption_description',
            'options' => [
                'tls' => 'TLS',
                'ssl' => 'SSL',
                '' => 'None',
            ],
            'default' => config('mail.encryption', 'tls'),
            'validation' => ['nullable', 'string', 'in:tls,ssl,'],
        ])
        ->add('from_email', [
            'type' => 'email',
            'label' => 'core/base::general.from_email',
            'description' => 'core/base::general.from_email_description',
            'default' => config('mail.from.address'),
            'required' => true,
            'validation' => ['required', 'email', 'max:255'],
        ])
        ->add('from_name', [
            'type' => 'text',
            'label' => 'core/base::general.from_name',
            'description' => 'core/base::general.from_name_description',
            'default' => config('mail.from.name', config('app.name')),
            'required' => true,
            'validation' => ['required', 'string', 'max:255'],
        ])
        ->add('email_signature', [
            'type' => 'textarea',
            'label' => 'core/base::general.email_signature',
            'description' => 'core/base::general.email_signature_description',
            'validation' => ['nullable', 'string', 'max:1000'],
            'attributes' => ['rows' => 4],
        ]);
    }

}
