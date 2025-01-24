<x-ui.layouts::app>
    <div class="row row-deck row-cards">
        <x-ui::card>
            <x-slot name="header" value="{{ __('Role Manager') }}">
                {{ __('Role Manager') }}
            </x-slot>

            @livewire('core/base::role-table')
        </x-ui::card>
    </div>
</x-ui.layouts::app>
