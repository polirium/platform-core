<?php

namespace Polirium\Core\UI\Support\WireUi;

use Illuminate\Contracts\Support\Htmlable;

class Html implements Htmlable {
    public function __construct(
        public string $html
    ) {
    }

    public function toHtml(): string
    {
        return $this->html;
    }
}
