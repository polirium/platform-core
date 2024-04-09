<div>
    <x-form::select style="width: 200px;" wire:model.live="branch_id" tomselect :options="$branches" />
</div>

@push('scripts')
    <script>
        Livewire.on('window-location-reload', function (e) {
            window.location.reload();
        });
    </script>
@endpush
