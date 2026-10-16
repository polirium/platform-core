<?php

namespace Polirium\Core\Base\Menu;

use Closure;

class MenuManager
{
    /** @var array<string, MenuBuilder> */
    private array $menus = [];

    public function make(string $name, Closure $callback): MenuBuilder
    {
        $menu = $this->menus[$name] = new MenuBuilder($name);
        $callback($menu);
        $menu->markActiveItems();

        return $menu;
    }

    public function get(string $name): ?MenuBuilder
    {
        return $this->menus[$name] ?? null;
    }
}
