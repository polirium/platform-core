<x-ui.layouts::app>
<div class="container-xl">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <h2 class="page-title d-flex align-items-center gap-2">
                    {!! tabler_icon('settings', ['class' => 'icon']) !!}
                    Media Settings
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('media.index') }}" class="btn btn-outline-secondary">
                    {!! tabler_icon('arrow-left', ['class' => 'icon']) !!}
                    Quay lại Media Manager
                </a>
            </div>
        </div>
    </div>

    <livewire:core/media::media-settings />
</div>
</x-ui.layouts::app>
