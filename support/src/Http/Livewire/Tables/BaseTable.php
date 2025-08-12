<?php

namespace Polirium\Core\Support\Http\Livewire\Tables;

use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

class BaseTable extends PowerGridComponent
{
    use WithExport;

    public function customThemeClass(): ?string
    {
        return \Polirium\Core\UI\Theme\PoliPowerGrid::class;
    }
}
