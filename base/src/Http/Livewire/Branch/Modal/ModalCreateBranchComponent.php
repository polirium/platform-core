<?php

namespace Polirium\Core\Base\Http\Livewire\Branch\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\Branch\Branch;
use Polirium\Core\Base\Http\Models\District;
use Polirium\Core\Base\Http\Models\Province;
use Polirium\Core\Base\Http\Models\Ward;

class ModalCreateBranchComponent extends Component
{
    public $branch_id = null;

    public $name = '';
    public $phone = '';
    public $phone_2 = '';
    public $email = '';
    public $address = '';
    public $province_id = null;
    public $district_id = null;
    public $ward_id = null;

    public $list = [];

    protected function rules()
    {
        return [
            'name' => "required|unique:branches,name,{$this->branch_id},id",
            'phone' => "nullable|unique:branches,phone,{$this->branch_id},id",
            'phone_2' => "nullable|unique:branches,phone_2,{$this->branch_id},id",
            'email' => "nullable|email|string|max:255|unique:branches,email,{$this->branch_id},id",
            'address' => 'nullable|string|max:255',
            'province_id' => 'nullable|numeric|integer',
            'district_id' => 'nullable|numeric|integer',
            'ward_id' => 'nullable|numeric|integer',
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

    public function updatedProvinceId($value)
    {
        if ($value) {
            $this->list['districts'] = District::select(['id', 'name'])->where('province_id', $value)->pluck('name', 'id')->all();
            $this->list['wards'] = [];
        } else {
            $this->list['districts'] = [];
            $this->list['wards'] = [];
        }

        $this->district_id = null;
        $this->ward_id = null;
    }

    public function updatedDistrictId($value)
    {
        if ($value) {
            $this->list['wards'] = Ward::select(['id', 'name'])->where('district_id', $value)->pluck('name', 'id')->all();
        } else {
            $this->list['wards'] = [];
        }

        $this->ward_id = null;
    }

    public function render()
    {
        return view('core/base::branch.modal.modal-create-branch');
    }

    public function resetInput()
    {
        $this->name = '';
        $this->phone = '';
        $this->phone_2 = '';
        $this->email = '';
        $this->address = '';
        $this->province_id = null;
        $this->district_id = null;
        $this->ward_id = null;
        $this->branch_id = null;
    }

    #[On('show-modal-create-branch')]
    public function showModal($id = null)
    {
        $this->branch_id = $id;
        if ($id) {
            $branch = Branch::findOrFail($id);

            $this->name = $branch->name;
            $this->phone = $branch->phone;
            $this->phone_2 = $branch->phone_2;
            $this->email = $branch->email;
            $this->address = $branch->address;
            $this->province_id = $branch->province_id;

            // Load districts and wards for the existing branch
            if ($branch->province_id) {
                $this->list['districts'] = District::select(['id', 'name'])->where('province_id', $branch->province_id)->pluck('name', 'id')->all();
            }
            if ($branch->district_id) {
                $this->list['wards'] = Ward::select(['id', 'name'])->where('district_id', $branch->district_id)->pluck('name', 'id')->all();
            }

            $this->district_id = $branch->district_id;
            $this->ward_id = $branch->ward_id;
        } else {
            $this->resetInput();
        }
        $this->dispatch('poli.modal', ['modal-create-branch', 'show']);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone ?: null,
            'phone_2' => $this->phone_2 ?: null,
            'email' => $this->email ?: null,
            'address' => $this->address ?: null,
            'province_id' => $this->province_id,
            'district_id' => $this->district_id,
            'ward_id' => $this->ward_id,
            'user_id' => auth()->id(),
        ];

        if ($this->branch_id) {
            $branch = Branch::findOrFail($this->branch_id);
            $branch->update($data);
        } else {
            Branch::create($data);
        }

        $this->dispatch('refresh-datatable-branches');
        $this->dispatch('poli.modal', ['modal-create-branch', 'hide']);
        $this->resetInput();
    }
}
