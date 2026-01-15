<x-ui.layouts::app>
    <x-slot:title>{{ __('core/base::general.activity_log') }}</x-slot:title>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <livewire:core-base-activity-log-table />
                </div>
            </div>
        </div>
    </div>
</x-ui.layouts::app>
