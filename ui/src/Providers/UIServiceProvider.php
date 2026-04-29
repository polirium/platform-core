<?php

namespace Polirium\Core\UI\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Polirium\Core\Base\Helpers\BaseHelper;
use Polirium\Core\Support\Providers\PoliriumBaseServiceProvider;
use Polirium\Core\UI\Support\Assets;

class UIServiceProvider extends PoliriumBaseServiceProvider
{
    public function boot(): void
    {
        $this->setNamespace('core/ui')
            ->loadConfigurations(['assets', 'components'])
            ->loadViews()
            ->loadTranslations()
            ->publishAssets();

        // FIX: Clear facade resolved instance cache to ensure fresh instance with loaded config
        \Polirium\Core\UI\Facades\Assets::clearResolvedInstance();

        $this->registerBladeComponents();
    }

    public function register(): void
    {
        BaseHelper::autoload(__DIR__ . '/../../helpers');

        // FIX: Use binding instead of singleton to avoid stale config
        // Singleton was created before config was loaded, causing empty assets
        $this->app->bind('polirium:assets', function () {
            return new Assets();
        });
    }

    protected function registerBladeComponents(): void
    {
        Blade::componentNamespace('Polirium\\Core\\UI\\View\\Components\\Layouts', 'ui.layouts');
        Blade::componentNamespace('Polirium\\Core\\UI\\View\\Components\\Header', 'ui.header');

        // Register 'ui' namespace for class-based components (x-ui::button, etc.)
        Blade::componentNamespace('Polirium\\Core\\UI\\View\\Components', 'ui');

        Blade::anonymousComponentPath(__DIR__ . '/../../resources/views/components/table', 'ui.table');
        Blade::anonymousComponentPath(__DIR__ . '/../../resources/views/components/interface', 'ui');
        // Load old form components from 'forms' directory with 'form' namespace (backward compatibility)
        Blade::anonymousComponentPath(__DIR__ . '/../../resources/views/components/forms', 'form');

        // Register new form components individually with view paths
        Blade::component('core/ui::components/form/input', 'ui.form.input');
        Blade::component('core/ui::components/form/textarea', 'ui.form.textarea');
        Blade::component('core/ui::components/form/select', 'ui.form.select');
        Blade::component('core/ui::components/form/checkbox', 'ui.form.checkbox');
        Blade::component('core/ui::components/form/radio', 'ui.form.radio');
        Blade::component('core/ui::components/form/group', 'ui.form.group');
        Blade::component('core/ui::components/form/actions', 'ui.form.actions');
        Blade::component('core/ui::components/form/tabs', 'ui.form.tabs');
        Blade::component('core/ui::components/form/image-upload', 'ui.form.image-upload');

        $this->callAfterResolving(BladeCompiler::class, static function (BladeCompiler $blade): void {
            foreach (config('core.ui.components', []) as $alias => $component) {
                $blade->component($component['class'], $component['alias']);
            }
        });
    }
}
