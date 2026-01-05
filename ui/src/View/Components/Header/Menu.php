<?php

namespace Polirium\Core\UI\View\Components\Header;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\Component;
use Lavary\Menu\Facade as MenuFacade;
use Lavary\Menu\Item;

class Menu extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $menu = MenuFacade::get('polirium.core.menu');

        if (!$menu) {
            $menuItems = collect([]);
        } else {
            // Filter menu items by permission
            $menuItems = $this->filterMenuItemsByPermission($menu->roots());
        }

        return view('core/ui::components.header.menu', compact('menuItems'));
    }

    /**
     * Filter menu items based on user permissions
     */
    protected function filterMenuItemsByPermission($items): \Illuminate\Support\Collection
    {
        $user = auth()->user();

        // If no user, return empty
        if (!$user) {
            return collect([]);
        }

        // Super admin can see everything
        if ($user->super_admin) {
            return $items;
        }

        $filteredItems = collect([]);

        foreach ($items as $item) {
            // Get permission from menu item data
            $permission = $item->data('permission');

            // If no permission specified, item is visible
            if (empty($permission)) {
                // Check if this is a parent menu with children
                if ($item->hasChildren()) {
                    // Filter children first
                    $filteredChildren = $this->filterMenuItemsByPermission($item->children());

                    // Only include parent if it has visible children
                    if ($filteredChildren->count() > 0) {
                        $filteredItems->push($item);
                    }
                } else {
                    $filteredItems->push($item);
                }
                continue;
            }

            // Check permission
            $hasPermission = false;
            if (is_array($permission)) {
                foreach ($permission as $perm) {
                    if ($user->can($perm)) {
                        $hasPermission = true;
                        break;
                    }
                }
            } else {
                $hasPermission = $user->can($permission);
            }

            if ($hasPermission) {
                $filteredItems->push($item);
            }
        }

        return $filteredItems;
    }
}
