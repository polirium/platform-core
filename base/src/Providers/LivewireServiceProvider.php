<?php

namespace Polirium\Core\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Polirium\Core\Base\Traits\LivewireComponentsTrait;

class LivewireServiceProvider extends ServiceProvider
{
    use LivewireComponentsTrait;

    public function register(): void
    {
        // Register custom Finder BEFORE Livewire's ServiceProvider
        // This ensures our Finder is used instead of the default one
        $this->app->singleton('livewire.finder', function () {
            $components = $this->getLivewireComponent();
            $finder = new \Polirium\Core\Base\Extensions\Finder();
            $finder->setCustomComponents($components);

            return $finder;
        });
    }

    public function boot(): void
    {
        // Nothing needed here - components are set in register()
    }
}
