<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-create-branch" :header="trans(($branch_id ? 'Sửa' : 'Tạo') . ' chi nhánh')" class="modal-xl">
            <div class="mb-3">
                <x-form::input wire:model="branch.name" :label="trans('Tên chi nhánh')" />
            </div>

            <div class="mb-3 row">
                <div class="col-md-6">
                    <x-form::input wire:model="branch.phone" :label="trans('SĐT')" />
                </div>
                <div class="col-md-6">
                    <x-form::input wire:model="branch.phone_2" :label="trans('SĐT phụ')" />
                </div>
            </div>

            <div class="mb-3">
                <x-form::input wire:model="branch.email" :label="trans('Email')" />
            </div>

            <div class="mb-3">
                <x-form::textarea wire:model="branch.address" :label="trans('Địa chỉ')" />
            </div>

            <div class="row">
                <div class="mb-3 col-md-12">
                    <x-form::select wire:model.live="branch.province_id" :label="trans('Thành phố/Tỉnh')" :options="$list['provinces']" tomselect />
                </div>
                <div class="mb-3 col-md-12">
                    <x-form::select wire:model.live="branch.district_id" :label="trans('Quận/Huyện')" :options="$list['districts']" tomselect />
                </div>
                <div class="mb-3 col-md-12">
                    <x-form::select wire:model="branch.ward_id" :label="trans('Xã')" :options="$list['wards']" tomselect />
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
