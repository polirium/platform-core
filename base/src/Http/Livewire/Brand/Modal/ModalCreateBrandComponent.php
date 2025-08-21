<?php

namespace Polirium\Core\Base\Http\Livewire\Brand\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\Brand\Brand;

class ModalCreateBrandComponent extends Component
{
    public $brand_id = null;

    public $brand;

    public $list = [];

    protected function rules()
    {
        return [
            'brand.name' => "required|unique:brands,name,{$this->brand_id},id",
            'brand.user_id' => 'required|numeric|integer',
            'brand.note' => 'nullable|string|max:255',
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
        $this->reset('brand');
        $this->brand = new Brand();
    }

    #[On('show-modal-create-brand')]
    public function showModal($id = null)
    {
        $this->brand_id = $id;
        if ($id) {
            $this->brand = Brand::findOrFail($id);
        } else {
            $this->resetInput();
            $this->brand->user_id = auth()->id();
        }
        $this->dispatch('poli.modal', ['modal-create-brand', 'show']);
    }

    public function save()
    {
        $this->validate();

        $this->brand->save();

        $this->dispatch('refresh-datatable-brands');
        $this->dispatch('poli.modal', ['modal-create-brand', 'hide']);
        $this->resetInput();
    }
}
