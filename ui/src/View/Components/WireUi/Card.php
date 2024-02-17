<?php

namespace Polirium\Core\UI\View\Components\WireUi;

use Illuminate\Support\{Str, Stringable};
use Illuminate\View\Component;

class Card extends Component
{
    public function __construct(
        public ?string $padding = null,
        public ?string $shadow = null,
        public ?string $rounded = null,
        public ?string $color = null,
        public ?string $title = null,
        public ?string $action = null,
        public ?string $header = null,
        public ?string $footer = null,
        public ?string $cardClasses = '',
        public ?string $headerClasses = '',
        public ?string $footerClasses = '',
    ) {
        $padding ??= config('wireui.card.padding');
        $shadow  ??= config('wireui.card.shadow');
        $rounded ??= config('wireui.card.rounded');
        $color   ??= config('wireui.card.color');

        $this->padding       = $padding;
        $this->shadow        = $shadow;
        $this->rounded       = $rounded;
        $this->color         = $color;
        $this->cardClasses   = $this->setCardClasses($cardClasses);
        $this->headerClasses = $this->setHeaderClasses($headerClasses);
        $this->footerClasses = $this->setFooterClasses($footerClasses);
    }

    public function setCardClasses(?string $cardClasses): string
    {
        return Str::of('card')
            ->append(" {$this->shadow}")
            ->append(" {$this->rounded}")
            ->append(" {$this->color}")
            ->append(" {$cardClasses}");
    }

    public function setHeaderClasses(?string $headerClasses): string
    {
        if (Str::contains($headerClasses, 'dark:border')) {
            return Str::of('card-header')
                ->replace('dark:border-0', '')
                ->append(" {$headerClasses}");
        }

        return Str::of('card-header')
            ->append(" {$headerClasses}");
    }

    public function setFooterClasses(?string $footerClasses): string
    {
        return Str::of('card-footer')
            ->append(" {$this->rounded}")
            ->append(" {$footerClasses}");
    }

    public function render()
    {
        return view('core/ui::components.wireui.card');
    }
}
