<?php

namespace Polirium\Core\Base\Http\Livewire\Branch\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\Branch\BranchTakingAddress;
use Polirium\Core\Base\Http\Models\Province;

class ModalCreateBranchTakingAddressComponent extends Component
{
    public $address_id = null;

    public $branch;

    public $list = [];

    protected function rules()
    {
        return [
            'branch.branch_id' => 'required|numeric|integer',
            'branch.address' => 'nullable|string|max:255',
            'branch.province_id' => 'nullable|numeric|integer',
            'branch.district_id' => 'nullable|numeric|integer',
            'branch.ward_id' => 'nullable|numeric|integer',
            'branch.phone' => 'nullable|string|max:255',
            'branch.user_id' => 'required|numeric|integer',
        ];
    }

    public function mount()
    {
        $this->list['provinces'] = Province::where('published', true)->select(['id', 'name'])->pluck('name', 'id')->all();
        $this->list['districts'] = [];
        $this->list['wards'] = [];
        $this->resetInput();
    }

    public function updated($value)
    {
        $this->validateOnly($value);
    }

    public function updatedBranch($value, $key)
    {
        if ($key == 'province_id') {
            if ($value) {
                $this->list['districts'] = get_districts($value);
                $this->list['wards'] = get_wards_by_province($value);
            } else {
                $this->list['districts'] = [];
                $this->list['wards'] = [];
            }

            $this->branch->district_id = null;
            $this->branch->ward_id = null;
        } elseif ($key == 'district_id') {
            if ($value) {
                $this->list['wards'] = get_wards($value);
            } else {
                $this->list['wards'] = [];
            }

            $this->branch->ward_id = null;
        }
    }

    public function render()
    {
        return view('core/base::branch.modal.modal-create-branch-taking-address');
    }

    public function resetInput()
    {
        $this->list['provinces'] = get_provinces();
        $this->list['districts'] = [];
        $this->list['wards'] = [];
        $this->reset('branch');
        $this->branch = new BranchTakingAddress();
    }

    #[On('show-modal-create-branch-taking-address')]
    public function showModal($branch_id, $id = null)
    {
        $this->authorize($id ? 'branches.edit' : 'branches.edit');

        $this->list['provinces'] = get_provinces();
        $this->list['districts'] = [];
        $this->list['wards'] = [];

        $this->address_id = $id;

        if ($id) {
            $this->branch = BranchTakingAddress::findOrFail($id);

            $district_id = $this->branch->district_id;
            $ward_id = $this->branch->ward_id;

            if ($this->branch->province_id) {
                $this->list['provinces'] = get_provinces($this->branch->province_id);
                $this->list['districts'] = get_districts($this->branch->province_id, $district_id);
                $this->list['wards'] = get_wards_by_province($this->branch->province_id, $ward_id);
            }

            $this->branch->district_id = $district_id;
            $this->branch->ward_id = $ward_id;
        } else {
            $this->resetInput();
        }

        $this->branch->branch_id = $branch_id;
        $this->dispatch('poli.modal', ['modal-create-branch-taking-address', 'show']);
    }

    public function save()
    {
        $this->authorize('branches.edit');

        $this->branch->user_id = auth()->id();

        $this->validate();

        $this->branch->save();

        $this->dispatch('refresh-datatable-branches');
        $this->dispatch('poli.modal', ['modal-create-branch-taking-address', 'hide']);
        $this->resetInput();
    }
}
