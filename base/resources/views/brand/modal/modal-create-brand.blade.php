<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-create-brand" :header="trans(($brand_id ? 'Sửa' : 'Tạo') . ' thương hiệu')">
            <div class="mb-3">
                <x-form::input wire:model="name" :label="trans('Tên thương hiệu')" />
            </div>

            <div class="mb-3">
                <x-form::input wire:model="note" :label="trans('Note')" />
            </div>

            <x-slot name="footer">
                <button type="submit" class="btn btn-success">
                    {{ tabler_icon('device-floppy') }}
                    {{ trans('Lưu') }}
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>

