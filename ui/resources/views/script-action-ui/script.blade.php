<div>
    <script>
        Livewire.on('call-modal', (e) => {
            $(`#${e[0]}`).modal(e[1]);
        });
    </script>
</div>