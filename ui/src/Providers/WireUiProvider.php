<?php

namespace Polirium\Core\UI\Providers;

use Illuminate\Support\Facades\Blade;
use Polirium\Core\Support\Providers\PoliriumBaseServiceProvider;

class WireUiProvider extends PoliriumBaseServiceProvider
{
    public function boot()
    {
        Blade::componentNamespace('Polirium\\Core\\UI\\View\\Components\\WireUi', 'polirium');
    }
}
