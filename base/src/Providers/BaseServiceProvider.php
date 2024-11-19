<?php

namespace Polirium\Core\Base\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Config;
use Polirium\Core\Base\Exceptions\Handler;
use Polirium\Core\Base\Helpers\Assets;
use Polirium\Core\Base\Helpers\BaseHelper;
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

        $this->app['events']->listen(RouteMatched::class, function () {
            $this->app->register(MenuServiceProvider::class);
        });

        Config::set('auth.providers.users.model', \Polirium\Core\Base\Http\Models\User::class);
        Config::set('livewire-powergrid.theme', \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class);

        $this->commands([
            \Polirium\Core\Base\Commands\InstallCommand::class,
            \Polirium\Core\Base\Commands\UserCreateCommand::class,
        ]);
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
}
