<?php

namespace Polirium\Core\UI\View\Components\WireUi;

use Closure;

class CircleButton extends Button
{
    public function __construct(
        public bool $rounded = true,
        public bool $squared = false,
        public bool $outline = false,
        public bool $flat = false,
        public bool $full = false,
        public ?string $color = null,
        public ?string $size = null,
        public ?string $label = null,
        public ?string $icon = null,
        public ?string $rightIcon = null,
        public ?string $spinner = null,
        public ?string $loadingDelay = null,
        public ?string $href = null
    ) {
        parent::__construct(
            rounded: true,
            squared: false,
            outline: $outline,
            flat: $flat,
            full: false,
            color: $color,
            size: $size,
            label: $label,
            icon: $icon,
            rightIcon: null,
            spinner: $spinner,
            loadingDelay: $loadingDelay,
            href: $href
        );
    }

    public function render(): Closure
    {
        return function (array $data) {
            return view('core/ui::components.wireui.circle-button', $this->mergeData($data));
        };
    }

    public function sizes(): array
    {
        return [
            'xs'          => 'btn-xs',
            'sm'          => 'btn-sm',
            self::DEFAULT => '',
            'lg'          => 'btn-lg',
        ];
    }

    public function iconSizes(): array
    {
        return [
            'xs'          => 'btn-xs',
            'sm'          => 'btn-sm',
            self::DEFAULT => '',
            'lg'          => 'btn-lg',
        ];
    }
}
