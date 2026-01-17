<?php

namespace Polirium\Core\Base\Http\Livewire\Table;

use Livewire\Component;
use Polirium\Core\Base\Http\Models\User;

class ActivityLogFilterSidebarComponent extends Component
{
    public $search = [
        'user' => '',
        'action' => '',
    ];

    public $users = [];
    public $actions = [];

    public function mount()
    {
        $this->users = User::pluck('name', 'id')->all();
        $this->actions = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
        ];
    }

    public function updatedSearch($value, $key)
    {
        $this->dispatch("datatable-activity-log-filter", $value, $key);
    }

    public function clearFilter()
    {
        $this->search = ['user' => '', 'action' => ''];
        $this->dispatch("datatable-activity-log-filter-clear");
    }

    public function render()
    {
        return view('core/base::activity-log.filter-sidebar');
    }
}
