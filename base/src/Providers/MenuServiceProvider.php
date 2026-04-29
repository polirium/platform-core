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

        // Always create menu (even if empty) to prevent null errors
        Menu::make('polirium.core.menu', function ($menu) {
            if (empty($this->tree) || empty($this->tree['root'])) {
                return;
            }

            foreach ($this->tree['root'] as $treeKey => $element) {
                if (! isset($this->getMenus[$element])) {
                    continue;
                }

                $menuData = $this->getMenus[$element];

                $options = [
                    'id' => $menuData['id'],
                    'icon' => $menuData['icon'] ?? '',
                ];

                if (! empty($menuData['route'])) {
                    $options['route'] = $menuData['route'];
                } elseif (! empty($menuData['url'])) {
                    $options['url'] = $menuData['url'];
                } else {
                    $options['url'] = 'javascript:void(0);';
                }

                // Make Parent Menu with permission data
                $item = $menu->add($menuData['name'], $options);

                // Attach permission data to menu item
                if (! empty($menuData['permission'])) {
                    $item->data('permission', $menuData['permission']);
                }

                //Add Sub Menu
                if (isset($this->tree[$element])) {
                    $this->loopTree($element, $menu);
                }
            }
        });
    }

    public function loopTree(string $element, \Lavary\Menu\Builder $menu): void
    {
        foreach ($this->tree[$element] as $subKey => $subElement) {
            if (! isset($this->getMenus[$subElement])) {
                continue;
            }

            $menuData = $this->getMenus[$subElement];

            $options = [
                'id' => $menuData['id'],
                'icon' => $menuData['icon'] ?? '',
            ];

            if (! empty($menuData['route'])) {
                $options['route'] = $menuData['route'];
            } elseif (! empty($menuData['url'])) {
                $options['url'] = $menuData['url'];
            } else {
                $options['url'] = 'javascript:void(0);';
            }

            $item = $menu->find($menuData['parent'])
                ->add($menuData['name'], $options);

            // Attach permission data to menu item
            if (! empty($menuData['permission'])) {
                $item->data('permission', $menuData['permission']);
            }

            if (isset($this->tree[$subElement])) {
                $this->loopTree($subElement, $menu);
            }
        }
    }
}
