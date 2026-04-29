<x-ui.layouts::app>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ trans('core/base::general.branches') }}
                    </h2>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            @livewire('core/base::branch.filter-sidebar')
        </div>
        <div class="col-md-9">
            <x-ui::card>
                @livewire('core/base::branch-table')
            </x-ui::card>
        </div>
    </div>

    @livewire('core/base::branch.modal.modal-create-branch')
    @livewire('core/base::branch.modal.modal-create-branch-taking-address')
</x-ui.layouts::app>
