<?php

namespace Polirium\Core\UI\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Polirium\Core\UI\View\PoliriumComponent;

class Button extends PoliriumComponent
{
    // public string $color = 'primary';
    // public string $class = 'btn ';
    // public string $size = 'md';
    // public ?string $href = null;
    // public ?string $icon = null;
    // public ?string $label = null;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $color = 'primary',
        public string $size = 'md',
        public string $class = 'btn ',
        public ?string $href = null,
        public ?string $icon = null,
        public bool $outline = false,
        public bool $ghost = false,
        public bool $square = false,
        public bool $pill = false,
        public bool $active = false,
        public string $label = '',
    ) {
        $style = "";
        if ($this->outline) {
            $style = "outline-";
        }

        if ($this->ghost) {
            $style = "ghost-";
        }

        $this->class .= "btn-{$style}{$this->color}";

        $this->class .= ' btn-' . $this->size;

        $this->class .= ' ' . $this->class;

        if ($this->icon && empty($this->label)) {
            $this->class .= ' btn-icon ';
        }

        if ($this->active) {
            $this->class .= ' active';
        }

        if ($this->square) {
            $this->class .= ' btn-square ';
        }

        if ($this->pill) {
            $this->class .= ' btn-pill ';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('core/ui::components.button', [
            'class' => $this->class,
            'icon' => $this->icon,
            'label' => $this->label,
            'tag' => $this->checkButtonOrATag(),
        ]);
    }

    public function checkButtonOrATag(): string
    {
        if (! empty($this->href)) {
            return "a href={$this->href}";
        }

        return "button";
    }
}
