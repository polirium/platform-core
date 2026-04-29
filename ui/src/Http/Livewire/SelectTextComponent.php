<?php

namespace Polirium\Core\UI\Http\Livewire;

class SelectTextComponent extends BaseInline
{
    public array $options = [];

    public function render()
    {
        return view('core/ui::inline.select');
    }
}
