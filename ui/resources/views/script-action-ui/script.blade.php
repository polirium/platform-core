<div>
    <script>
        Livewire.on('call-modal', (e) => {
            const modalId = e[0];
            const action = e[1] || 'show';

            const modalElement = document.getElementById(modalId);
            if (!modalElement) {
                console.error('Modal element not found:', modalId);
                return;
            }

            if (action === 'show') {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else if (action === 'hide') {
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    // Nếu chưa có instance, tạo mới và ẩn luôn
                    const newModal = new bootstrap.Modal(modalElement);
                    newModal.hide();
                }
            }
        });
    </script>
</div>
