<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-create-branch-taking-address" :header="trans(($address_id ? __('core/base::general.edit_address_text') : __('core/base::general.create_address_text')) . ' ' . __('core/base::general.address_text'))" class="modal-xl">
            <div class="mb-3">
                <x-form::input wire:model="branch.address" :label="__('core/base::general.address')" />
            </div>

            <div class="mb-3">
                <x-form::select wire:model.live="branch.province_id" :label="__('core/base::general.city_province')" :options="$list['provinces']" tomselect />
            </div>
            <div class="mb-3">
                <x-form::select wire:model.live="branch.district_id" :label="__('core/base::general.district')" :options="$list['districts']" tomselect />
            </div>
            <div class="mb-3">
                <x-form::select wire:model="branch.ward_id" :label="__('core/base::general.ward')" :options="$list['wards']" tomselect />
            </div>

            <div class="mb-3">
                <x-form::input wire:model="branch.phone" :label="__('core/base::general.phone')" />
            </div>

            <x-slot name="footer">
                <button type="submit" class="btn btn-success">
                    {{ tabler_icon('device-floppy') }}
                    {{ __('core/base::general.save') }}
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
