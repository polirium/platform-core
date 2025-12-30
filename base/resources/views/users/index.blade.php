<x-ui.layouts::app>
    <div class="row row-deck row-cards">
        <x-ui::card>
            <x-slot name="header" value="{{ __('User Manager') }}">
                {{ __('User Manager') }}
            </x-slot>

            <x-slot name="action">
                <x-ui.button color="success" onclick="Livewire.dispatch('show-modal-create-user');">{{ __('Create User') }}</x-ui.button>
            </x-slot>

            @livewire('core/base::user-table')

            @livewire('core/base::user.modal')
            @livewire('core/base::user.modal.delete')
        </x-ui::card>
    </div>
</x-ui.layouts::app>
