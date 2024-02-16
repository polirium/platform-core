<?php

namespace Polirium\Core\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Lavary\Menu\Facade as Menu;
use Polirium\Core\Base\Traits\GetMenuDataTrait;

class MenuServiceProvider extends ServiceProvider
{
    use GetMenuDataTrait;

    public $getMenus = [];

    public $tree = [];

    public function __construct()
    {
        $this->getMenus = $this->getAvailableMenus();
        $this->tree = $this->getMenuTree($this->getMenus);
    }

    public function boot()
    {
        $this->getMenus = $this->getAvailableMenus();
        $this->tree = $this->getMenuTree($this->getMenus);

        if(! empty($this->getMenus)) {
            Menu::make('polirium.core.menu', function ($menu) {
                foreach ($this->tree['root'] as $treeKey => $element) {
                    //Make Parent Menu
                    $menu->add($this->getMenus[$element]['name'], [
                        'route' => $this->getMenus[$element]['route'],
                        'id' => $this->getMenus[$element]['id'],
                        'icon' => $this->getMenus[$element]['icon'] ?? '',
                    ]);

                    //Add Sub Menu
                    if (isset($this->tree[$element])) {
                        $this->loopTree($element, $menu);
                    }
                }
            });
        }
    }

    public function loopTree(string $element, \Lavary\Menu\Builder $menu): void
    {
        foreach ($this->tree[$element] as $subKey => $subElement) {
            $menu->find($this->getMenus[$subElement]['parent'])
                ->add($this->getMenus[$subElement]['name'], [
                    'route' => $this->getMenus[$subElement]['route'],
                    'id' => $this->getMenus[$subElement]['id'],
                    'icon' => $this->getMenus[$subElement]['icon'] ?? '',
                ]);

            if (isset($this->tree[$subElement])) {
                $this->loopTree($subElement, $menu);
            }
        }
    }
}
