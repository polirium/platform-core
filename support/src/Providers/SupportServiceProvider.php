<?php

namespace Polirium\Core\Support\Providers;

use Polirium\Core\Base\Helpers\BaseHelper;
use Polirium\Core\Support\Support;

class SupportServiceProvider extends PoliriumBaseServiceProvider
{
    public function boot()
    {
        $this->setNamespace('core/support');
    }

    public function register(): void
    {
        BaseHelper::autoload(__DIR__ . '/../../helpers');

        $this->app->singleton('polirium:support', function () {
            return new Support();
        });
    }
}
