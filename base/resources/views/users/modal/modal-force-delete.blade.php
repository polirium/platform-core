<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-force-delete-user" header="{{ trans('core/base::general.permanent_delete') }}" class="modal-dialog modal-dialog-centered">

            <h2>{{ trans('core/base::general.permanent_delete') }}</h2>

            <p>{{ __('Bạn có chắc chắn muốn xóa vĩnh viễn người dùng này?') }}</p>
            <p><strong style="color: red;">{{ __('⚠️ Hành động này KHÔNG thể hoàn tác!') }}</strong></p>
            <p>{{ __('Tất cả dữ liệu của người dùng sẽ bị xóa vĩnh viễn khỏi hệ thống.') }}</p>
            @isset($user)
                <p>{{ trans('core/base::general.user_name_label') }} <b>{{ $user->name }}</b></p>
            @endisset


            <x-slot name="footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">{{ trans('core/base::general.cancel') }}</button>
                <button type="submit" class="btn btn-danger">
                    {!! tabler_icon('trash-x') !!}
                    {{ trans('core/base::general.permanent_delete') }}
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
