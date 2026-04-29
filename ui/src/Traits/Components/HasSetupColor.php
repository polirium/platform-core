<?php

namespace Polirium\Core\UI\Traits\Components;

use Polirium\Core\UI\Enum\Components\Color;

trait HasSetupColor
{
    protected function isColor(array|string $color): string
    {
        if (is_array($color)) {
            foreach ($color as $item) {
                if (! in_array($item, Color::toArray())) {
                    return $item;
                }
            }

            return Color::PRIMARY->value;
        }

        return in_array($color, Color::toArray());
    }

}
