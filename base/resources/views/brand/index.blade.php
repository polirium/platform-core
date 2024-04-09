<x-ui.layouts::app>
    <div class="row">
        <div class="col-md-3">
            <x-ui::card></x-ui::card>
        </div>
        <div class="col-md-9">
            <x-ui::card>
                @livewire('core/base::brand-table')
            </x-ui::card>
        </div>
    </div>

    @livewire('core/base::brand.modal.modal-create-brand')
</x-ui.layouts::app>
