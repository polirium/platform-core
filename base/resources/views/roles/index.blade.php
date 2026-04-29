<x-ui.layouts::app>
    <x-slot:title>{{ __('core/base::general.role_management') }}</x-slot:title>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('core/base::general.roles') }}
                    </h2>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            @livewire('core/base::roles.filter-sidebar')
        </div>
        <div class="col-md-9">
            <x-ui::card>


                @livewire('core/base::role-table')
            </x-ui::card>
        </div>
    </div>

    @livewire('core/base::roles.modal.modal-create-role')
</x-ui.layouts::app>
