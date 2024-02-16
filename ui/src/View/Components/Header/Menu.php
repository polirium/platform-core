<?php

namespace Polirium\Core\UI\View\Components\Header;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Lavary\Menu\Facade as MenuFacade;

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
        $menuItems = MenuFacade::get('polirium.core.menu')->roots();

        return view('core/ui::components.header.menu', compact('menuItems'));
    }
}
