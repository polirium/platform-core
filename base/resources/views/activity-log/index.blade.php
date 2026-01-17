<x-ui.layouts::app>
    <x-slot:title>{{ __('core/base::general.activity_log') }}</x-slot:title>

    <div class="row">
        <div class="col-md-3">
            @livewire('core/base::activity-log.filter-sidebar')
        </div>
        <div class="col-md-9">
            <x-ui::card>
                <x-slot name="header">
                    <div class="d-flex align-items-center">
                        {!! tabler_icon('history', ['class' => 'icon me-2']) !!}
                        {{ __('core/base::general.activity_log') }}
                    </div>
                </x-slot>

                <livewire:core-base-activity-log-table />
            </x-ui::card>
        </div>
    </div>
</x-ui.layouts::app>
