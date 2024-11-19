<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-create-user" header="{{ __('Create User') }}" class="modal-xl">

            <h2>{{ __('Create User') }}</h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <x-form::input wire:model="user.first_name" :label="trans('First name')" />
                    </div>

                    <div class="mb-3">
                        <x-form::input wire:model="user.last_name" :label="trans('Last name')" />
                    </div>

                    <div class="mb-3">
                        <x-form::input wire:model="user.phone" :label="trans('Phone')" />
                    </div>
                </div>



                <div class="col-md-6">
                    <div class="mb-3">
                        <x-form::input wire:model="user.email" :label="trans('Email')" />
                    </div>

                    <div class="mb-3">
                        <x-form::input wire:model="user.username" :label="trans('Username')" />
                    </div>

                    <div class="mb-3">
                        <x-form::input wire:model="user.password" :label="trans('Password')" />
                    </div>

                    <div class="mb-3">
                        <x-form::input wire:model="user.password_confirmation" :label="trans('Password confirmation')" />
                    </div>
                </div>

            </div>

            <div class="mb-3">
                <x-form::input wire:model="user.note" :label="trans('Note')" />
            </div>

            {{-- <div class="mb-3">
                <x-form::select wire:model="user.role_id" :label="trans('Role')" :options="$list['roles']" tomselect />
            </div>

            <div class="mb-3">
                <x-form::select wire:model="user.status" :label="trans('Status')" :options="$list['statuses']" tomselect />
            </div> --}}

            <x-slot name="footer">
                <button type="submit" class="btn btn-success">
                    {{ tabler_icon('device-floppy') }}
                    {{ trans('Lưu') }}
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
