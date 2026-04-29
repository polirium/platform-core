<?php

namespace Polirium\Core\Base\Http\Livewire\Brand\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\Brand\Brand;

class ModalCreateBrandComponent extends Component
{
    public $brand_id = null;

    public $name = '';
    public $user_id = null;
    public $note = '';

    public $list = [];

    protected function rules()
    {
        return [
            'name' => "required|unique:brands,name,{$this->brand_id},id",
            'user_id' => 'required|numeric|integer',
            'note' => 'nullable|string|max:255',
        ];
    }

    public function mount()
    {
        $this->resetInput();
    }

    public function updated($value)
    {
        $this->validateOnly($value);
    }

    public function render()
    {
        return view('core/base::brand.modal.modal-create-brand');
    }

    public function resetInput()
    {
        $this->name = '';
        $this->user_id = auth()->id();
        $this->note = '';
        $this->brand_id = null;
    }

    #[On('show-modal-create-brand')]
    public function showModal($id = null)
    {
        $this->authorize($id ? 'brands.edit' : 'brands.create');

        $this->brand_id = $id;
        if ($id) {
            $brand = Brand::findOrFail($id);
            $this->name = $brand->name;
            $this->user_id = $brand->user_id;
            $this->note = $brand->note;
        } else {
            $this->resetInput();
        }
        $this->dispatch('poli.modal', ['modal-create-brand', 'show']);
    }

    public function save()
    {
        $this->authorize($this->brand_id ? 'brands.edit' : 'brands.create');

        $this->validate();

        $data = [
            'name' => $this->name,
            'user_id' => $this->user_id,
            'note' => $this->note,
        ];

        if ($this->brand_id) {
            $brand = Brand::findOrFail($this->brand_id);
            $brand->update($data);
        } else {
            Brand::create($data);
        }

        $this->dispatch('refresh-datatable-brands');
        $this->dispatch('poli.modal', ['modal-create-brand', 'hide']);
        $this->resetInput();
    }
}
