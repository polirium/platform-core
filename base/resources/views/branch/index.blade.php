<x-ui.layouts::app>
    <div class="row">
        <div class="col-md-3">
            <x-ui::card></x-ui::card>
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
