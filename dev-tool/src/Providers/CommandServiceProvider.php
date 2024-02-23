<?php

namespace Polirium\Core\DevTool\Providers;

use Illuminate\Support\ServiceProvider;
use Polirium\Core\DevTool\Commands\ModulesCreateCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            ModulesCreateCommand::class,
        ]);
    }
}
