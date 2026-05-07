<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-restore-user" header="{{ trans('core/base::general.restore') }}" class="modal-dialog modal-dialog-centered">

            <h2>{{ trans('core/base::general.restore') }}</h2>

            <p>{{ __('Bạn có chắc chắn muốn phục hồi người dùng này?') }}</p>
            <p>{{ __('Khi phục hồi, người dùng sẽ trở về trạng thái hoạt động.') }}</p>
            @isset($user)
                <p>{{ trans('core/base::general.user_name_label') }} <b>{{ $user->name }}</b></p>
            @endisset


            <x-slot name="footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">{{ trans('core/base::general.cancel') }}</button>
                <button type="submit" class="btn btn-success">
                    {!! tabler_icon('rotate-clockwise') !!}
                    {{ trans('core/base::general.restore') }}
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
