<x-ui.layouts::app>
    <div class="row row-deck row-cards">
        <x-ui::card>
            <x-slot name="header">
                {{ __('User Manager') }}
            </x-slot>

            <x-slot name="action">
                <x-ui.button color="success">{{ __('Create User') }}</x-ui.button>
            </x-slot>

            @livewire('core/base::user-table')
        </x-ui::card>
    </div>
</x-ui.layouts::app>
