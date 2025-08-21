<?php

namespace Polirium\Core\Base\Http\Livewire\Branch\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\Branch\BranchTakingAddress;
use Polirium\Core\Base\Http\Models\District;
use Polirium\Core\Base\Http\Models\Province;
use Polirium\Core\Base\Http\Models\Ward;

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
        $this->list['provinces'] = Province::select(['id', 'name'])->pluck('name', 'id')->all();
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
                $this->list['districts'] = District::select(['id', 'name'])->where('province_id', $value)->pluck('name', 'id')->all();
                $this->list['wards'] = [];
            } else {
                $this->list['districts'] = [];
                $this->list['wards'] = [];
            }

            $this->branch->district_id = null;
            $this->branch->ward_id = null;
        } elseif ($key == 'district_id') {
            if ($value) {
                $this->list['wards'] = Ward::select(['id', 'name'])->where('district_id', $value)->pluck('name', 'id')->all();
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
        $this->reset('branch');
        $this->branch = new BranchTakingAddress();
    }

    #[On('show-modal-create-branch-taking-address')]
    public function showModal($branch_id, $id = null)
    {
        $this->address_id = $id;

        if ($id) {
            $this->branch = BranchTakingAddress::findOrFail($id);

            $district_id = $this->branch->district_id;
            $ward_id = $this->branch->ward_id;

            $this->updatedBranch($this->branch->province_id, 'province_id');
            $this->updatedBranch($district_id, 'district_id');

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
        $this->branch->user_id = auth()->id();

        $this->validate();

        $this->branch->save();

        $this->dispatch('refresh-datatable-branches');
        $this->dispatch('poli.modal', ['modal-create-branch-taking-address', 'hide']);
        $this->resetInput();
    }
}
