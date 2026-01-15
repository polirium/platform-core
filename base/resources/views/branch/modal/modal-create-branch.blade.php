<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-create-branch" :header="trans(($branch_id ? __('core/base::general.edit_branch_text') : __('core/base::general.create_branch_text')) . ' ' . __('core/base::general.branch_text'))" class="modal-xl">
            <div class="row g-4">
                <div class="col-lg-6">
                    <x-ui.form.group :label="__('core/base::general.branch_information')" icon="building">
                        <div class="col-12">
                            <x-ui.form.input
                                wire:model="name"
                                :label="__('core/base::general.branch_name')"
                                :placeholder="__('core/base::general.enter_branch_name')"
                                icon="building"
                                required
                            />
                        </div>

                        <div class="col-md-6">
                            <x-ui.form.input
                                wire:model="phone"
                                :label="__('core/base::general.phone')"
                                :placeholder="__('core/base::general.phone_placeholder')"
                                icon="phone"
                                type="tel"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-ui.form.input
                                wire:model="phone_2"
                                :label="__('core/base::general.phone_secondary')"
                                :placeholder="__('core/base::general.phone_placeholder')"
                                icon="phone"
                                type="tel"
                            />
                        </div>

                        <div class="col-12">
                            <x-ui.form.input
                                wire:model="email"
                                :label="__('core/base::general.email')"
                                :placeholder="__('core/base::general.enter_email')"
                                icon="mail"
                                type="email"
                            />
                        </div>

                        <div class="col-12">
                            <x-ui.form.textarea
                                wire:model="address"
                                :label="__('core/base::general.address')"
                                :placeholder="__('core/base::general.enter_address')"
                                :rows="2"
                            />
                        </div>
                    </x-ui.form.group>
                </div>

                <div class="col-lg-6">
                    <x-ui.form.group :label="__('core/base::general.location')" icon="map-pin">
                        <div class="col-12">
                            <x-ui.form.select
                                wire:model.live="province_id"
                                :label="__('core/base::general.city_province')"
                                :placeholder="__('core/base::general.select_province')"
                                :options="$list['provinces']"
                            />
                        </div>

                        <div class="col-12">
                            <x-ui.form.select
                                wire:model.live="district_id"
                                :label="__('core/base::general.district')"
                                :placeholder="__('core/base::general.select_district')"
                                :options="$list['districts']"
                            />
                        </div>

                        <div class="col-12">
                            <x-ui.form.select
                                wire:model="ward_id"
                                :label="__('core/base::general.ward')"
                                :placeholder="__('core/base::general.select_ward')"
                                :options="$list['wards']"
                            />
                        </div>
                    </x-ui.form.group>
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">
                    {{ __('core/base::general.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <i class="ti ti-device-floppy me-1"></i>
                        {{ __('core/base::general.save') }}
                    </span>
                    <span wire:loading wire:target="save">
                        <i class="ti ti-loader-2 icon-spin me-1"></i>
                        {{ __('core/base::general.saving') }}
                    </span>
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
