<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-delete-user" header="{{ __('Delete User') }}" class="modal-dialog modal-dialog-centered">

            <h2>{{ __('Delete User') }}</h2>


            <p>{{ __('Are you sure you want to delete this user?') }}</p>
            <p> {{ __('This action is permanent and cannot be undone.') }}</p>
            <p>{{ __('Once the user is deleted, all of its resources and data will be permanently deleted.') }}</p>
            <p>{{ __('Please type the user name to confirm that you would like to permanently delete this user.') }}</p>
            @isset($user)
                <p>{{ __('User Name: ') }} <b>{{ $user->name }}</b></p>
            @endisset


            <x-slot name="footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">{{ trans('Cancel') }}</button>
                <button type="submit" class="btn btn-danger">
                    {{ tabler_icon('device-floppy') }}
                    {{ trans('Delete') }}
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
