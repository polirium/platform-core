<?php

namespace Polirium\Core\UI\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Polirium\Core\UI\Enum\Components\Color;
use Polirium\Core\UI\View\PoliriumComponent;

class Button extends PoliriumComponent
{
    public Color $color;
    public string $class = 'btn ';
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // dd($this->attributes);
        $this->color = Color::from($color);
        $this->class .= $this->color->value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('core/ui::components.button', [
            'class' => $this->class,
        ]);
    }
}
