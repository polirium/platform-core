<x-ui.layouts::app>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title m-0">
                        {{ trans('core/base::general.brands') }}
                    </h2>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            @livewire('core/base::brand.filter-sidebar')
        </div>
        <div class="col-md-9">
            <x-ui::card>
                @livewire('core/base::brand-table')
            </x-ui::card>
        </div>
    </div>

    @livewire('core/base::brand.modal.modal-create-brand')
</x-ui.layouts::app>
