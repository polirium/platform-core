<?php

namespace Polirium\Core\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Polirium\Core\Base\Menu\MenuBuilder;
use Polirium\Core\Base\Menu\MenuManager;
use Polirium\Core\Base\Traits\GetMenuDataTrait;

class MenuServiceProvider extends ServiceProvider
{
    use GetMenuDataTrait;

    public $getMenus = [];

    public $tree = [];

    public function boot()
    {
        $this->getMenus = $this->getAvailableMenus();
        $this->tree = $this->getMenuTree($this->getMenus);

        // Always create menu (even if empty) to prevent null errors
        $this->app->make(MenuManager::class)->make('polirium.core.menu', function (MenuBuilder $menu) {
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

                if (! empty($menuData['user_ids'])) {
                    $item->data('user_ids', $menuData['user_ids']);
                }

                //Add Sub Menu
                if (isset($this->tree[$element])) {
                    $this->loopTree($element, $menu);
                }
            }
        });
    }

    public function loopTree(string $element, MenuBuilder $menu): void
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

            if (! empty($menuData['user_ids'])) {
                $item->data('user_ids', $menuData['user_ids']);
            }

            if (isset($this->tree[$subElement])) {
                $this->loopTree($subElement, $menu);
            }
        }
    }
}
