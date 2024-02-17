<?php

namespace Polirium\Core\UI\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Polirium\Core\Base\Helpers\BaseHelper;
use Polirium\Core\Support\Providers\PoliriumBaseServiceProvider;
use Polirium\Core\UI\Support\Assets;

class UIServiceProvider extends PoliriumBaseServiceProvider
{
    public function boot()
    {
        $this->setNamespace('core/ui')
            ->loadConfigurations(['assets', 'components'])
            ->loadViews()
            ->loadTranslations()
            ->publishAssets();

        $this->registerBladeComponents();
    }

    public function register()
    {
        BaseHelper::autoload(__DIR__ . '/../../helpers');

        $this->app->singleton('polirium:assets', function () {
            return new Assets();
        });

    }

    protected function registerBladeComponents(): void
    {
        Blade::componentNamespace('Polirium\\Core\\UI\\View\\Components\\Layouts', 'ui.layouts');
        Blade::componentNamespace('Polirium\\Core\\UI\\View\\Components\\Header', 'ui.header');

        // $this->callAfterResolving(BladeCompiler::class, static function (BladeCompiler $blade): void {
        //     foreach(config('core.ui.components', []) as $alias => $component) {
        //         $blade->component($component['class'], $component['alias']);
        //     }
        // });
    }
}
