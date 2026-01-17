<?php

namespace Polirium\Core\Base\Http\Livewire\Brand;

use Livewire\Component;

class FilterSidebarComponent extends Component
{
    public $search = [
        'name' => '',
    ];

    public function updatedSearch($value, $key)
    {
        $this->dispatch("datatable-brand-filter", $value, $key);
    }

    public function clearFilter()
    {
        $this->search = ['name' => ''];
        $this->dispatch("datatable-brand-filter", '', 'name');
    }

    public function render()
    {
        return view('core/base::brand.filter-sidebar');
    }
}
