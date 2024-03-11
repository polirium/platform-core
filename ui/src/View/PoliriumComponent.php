<?php

namespace Polirium\Core\UI\View;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\{Arr, HtmlString, Str};

class PoliriumComponent extends Component
{
    public function render(): View|Closure|string
    {
        //
    }

    public function setupColor(array $attributes, string $prefix): string
    {
        foreach ($attributes as $attribute => $value) {
            if (in_array($attribute, Color::class)) {
                return ' ' . $prefix . '-' . $attribute;
            }
        }

        return ' ' . $prefix . '-primary'; // default class
    }

    protected function smartAttributes(mixed $attributes): void
    {
        collect(Arr::wrap($attributes))->filter()->each(
            fn ($value) => $this->smartAttributes[] = $value,
        );
    }
}
