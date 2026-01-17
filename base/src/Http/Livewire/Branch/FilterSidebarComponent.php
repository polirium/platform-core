<?php

namespace Polirium\Core\Base\Http\Livewire\Branch;

use Livewire\Component;

class FilterSidebarComponent extends Component
{
    public $search = [
        'name' => '',
    ];

    public function updatedSearch($value, $key)
    {
        $this->dispatch("datatable-branch-filter", $value, $key);
    }

    public function clearFilter()
    {
        $this->search = ['name' => ''];
        $this->dispatch("datatable-branch-filter", '', 'name');
    }

    public function render()
    {
        return view('core/base::branch.filter-sidebar');
    }
}
