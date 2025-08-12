<?php

namespace Polirium\Core\Base\Http\Livewire\Branch;

use Livewire\Component;
use Polirium\Core\Base\Http\Models\Branch\Branch;

class SwitchBranchComponent extends Component
{
    public $branch_id = null;

    public $branches;

    public function mount()
    {
        $this->branches = Branch::select(['id', 'name'])->pluck('name', 'id')->all();
        $this->branch_id = user_branch();
    }

    public function updatedBranchId()
    {
        user_branch($this->branch_id);
        $this->dispatch('window-location-reload');
    }

    public function render()
    {
        return view('core/base::branch.switch-branch');
    }
}
