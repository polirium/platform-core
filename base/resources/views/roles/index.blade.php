<x-ui.layouts::app>
    <x-slot:title>{{ __('core/base::general.role_management') }}</x-slot:title>

    <!-- Role Table Card -->
    <div class="row row-deck row-cards">
        <x-ui::card>
            <x-slot name="header">
                <div class="d-flex align-items-center">
                    <i class="ti ti-users-group me-2"></i>
                    {{ __('core/base::general.role_list') }}
                </div>
            </x-slot>

            @livewire('core/base::role-table')
            @livewire('core/base::roles.modal.modal-create-role')
        </x-ui::card>
    </div>
</x-ui.layouts::app>
