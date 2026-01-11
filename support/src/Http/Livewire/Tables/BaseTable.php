<?php

namespace Polirium\Core\Support\Http\Livewire\Tables;

use Polirium\Datatable\PowerGridComponent;
use Polirium\Datatable\Traits\WithExport;

class BaseTable extends PowerGridComponent
{
    use WithExport;

    public function customThemeClass(): ?string
    {
        return \Polirium\Core\UI\Theme\PoliPowerGrid::class;
    }
}
