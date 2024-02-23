<?php

namespace Polirium\Core\DevTool\Providers;

use Polirium\Core\Support\Providers\PoliriumBaseServiceProvider;

class DevToolServiceProvider extends PoliriumBaseServiceProvider
{
    public function boot(): void
    {
        $this->setNamespace('core/devtool');

        $this->app->register(CommandServiceProvider::class);
    }
}
