<?php

namespace Polirium\Core\Base\Http\Livewire\Roles;

use Livewire\Component;

class FilterSidebarComponent extends Component
{
    public $search = [
        'name' => '',
    ];

    public function updatedSearch($value, $key)
    {
        $this->dispatch('datatable-role-filter', $value, $key);
    }

    public function clearFilter()
    {
        $this->search = ['name' => ''];
        $this->dispatch('datatable-role-filter', '', 'name');
    }

    public function render()
    {
        return view('core/base::roles.filter-sidebar');
    }
}
