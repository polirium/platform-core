<?php

namespace Polirium\Core\UI\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Polirium\Core\UI\View\PoliriumComponent;

class Button extends PoliriumComponent
{
    public string $color = 'primary';
    public string $class = 'btn ';
    public string $size = 'md';
    public string $type = 'button';

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $color = 'primary',
        string $size = 'md',
        string $type = 'button',
        string $class = '',
    ) {
        $this->class .= 'btn-' . $color;
        $this->class .= ' btn-' . $size;
        $this->class .= ' ' . $class;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('core/ui::components.button', [
            'class' => $this->class,
            'type' => $this->type,
        ]);
    }
}
