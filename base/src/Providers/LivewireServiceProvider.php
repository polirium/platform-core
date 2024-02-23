<?php

namespace Polirium\Core\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Polirium\Core\Base\Traits\LivewireComponentsTrait;

class LivewireServiceProvider extends ServiceProvider
{
    use LivewireComponentsTrait;

    public function boot(): void
    {
        $components = $this->getLivewireComponent();

        foreach ($components as $component) {
            Livewire::component($component['alias'], $component['class']);
        }
    }
}
