<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-create-branch" :header="trans(($branch_id ? 'Sửa' : 'Tạo') . ' chi nhánh')" class="modal-xl">
            <div class="mb-3">
                <x-form::input wire:model="name" :label="trans('Tên chi nhánh')" />
            </div>

            <div class="mb-3 row">
                <div class="col-md-6">
                    <x-form::input wire:model="phone" :label="trans('SĐT')" />
                </div>
                <div class="col-md-6">
                    <x-form::input wire:model="phone_2" :label="trans('SĐT phụ')" />
                </div>
            </div>

            <div class="mb-3">
                <x-form::input wire:model="email" :label="trans('Email')" />
            </div>

            <div class="mb-3">
                <x-form::textarea wire:model="address" :label="trans('Địa chỉ')" />
            </div>

            <div class="row">
                <div class="mb-3 col-md-12">
                    <x-form::select wire:model.live="province_id" :label="trans('Thành phố/Tỉnh')" :options="$list['provinces']" tomselect />
                </div>
                <div class="mb-3 col-md-12">
                    <x-form::select wire:model.live="district_id" :label="trans('Quận/Huyện')" :options="$list['districts']" tomselect />
                </div>
                <div class="mb-3 col-md-12">
                    <x-form::select wire:model="ward_id" :label="trans('Xã')" :options="$list['wards']" tomselect />
                </div>
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
