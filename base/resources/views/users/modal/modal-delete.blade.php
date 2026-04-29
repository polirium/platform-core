<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-delete-user" header="{{ trans('core/base::general.delete_user') }}" class="modal-dialog modal-dialog-centered">

            <h2>{{ trans('core/base::general.delete_user') }}</h2>


            <p>{{ trans('core/base::general.confirm_delete_user') }}</p>
            <p> {{ trans('core/base::general.action_permanent') }}</p>
            <p>{{ trans('core/base::general.delete_user_warning') }}</p>
            <p>{{ trans('core/base::general.delete_user_confirm_hint') }}</p>
            @isset($user)
                <p>{{ trans('core/base::general.user_name_label') }} <b>{{ $user->name }}</b></p>
            @endisset


            <x-slot name="footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">{{ trans('core/base::general.cancel') }}</button>
                <button type="submit" class="btn btn-danger">
                    {{ tabler_icon('device-floppy') }}
                    {{ trans('core/base::general.delete') }}
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
