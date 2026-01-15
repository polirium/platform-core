<x-ui.layouts::app>
    <div class="row row-deck row-cards">
        <x-ui::card>
            <x-slot name="header" value="{{ __('Media Manager') }}">
                {{ __('core/media::media.media_manager') }}
            </x-slot>

            @livewire('core/media::media-manager')
        </x-ui::card>
    </div>
</x-ui.layouts::app>
