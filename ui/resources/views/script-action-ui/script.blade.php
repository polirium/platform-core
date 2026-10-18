<div>
    <script>
        // One Bootstrap instance per modal element: `new bootstrap.Modal()` here
        // used to stack a second backdrop that stayed after closing.
        Livewire.on('call-modal', (e) => {
            const modalId = e[0];
            const action = e[1] || 'show';

            const modalElement = document.getElementById(modalId);
            if (!modalElement) {
                console.error('Modal element not found:', modalId);
                return;
            }

            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            action === 'hide' ? modal.hide() : modal.show();
        });
    </script>
</div>
