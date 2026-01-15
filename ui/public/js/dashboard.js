/**
 * Dashboard Widget Sortable
 * Handles drag & drop functionality for dashboard widgets in edit mode
 */

let sortableInstance = null;

function initWidgetSortable() {
    const container = document.getElementById('widget-sortable-container');
    const dashboardContainer = document.querySelector('.dashboard-container');
    const isEditMode = dashboardContainer?.dataset.editMode === 'true';

    if (!container) return;

    // Destroy existing sortable if any
    if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
    }

    // Only init sortable in edit mode
    if (!isEditMode) return;

    sortableInstance = new Sortable(container, {
        animation: 150,
        handle: '.widget-drag-handle',
        draggable: '.widget-sortable-item',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function (evt) {
            const items = Array.from(container.querySelectorAll('.widget-sortable-item')).map(el => ({
                value: el.dataset.id
            }));
            Livewire.find(dashboardContainer.closest('[wire\\:id]')?.getAttribute('wire:id'))?.call('updateOrder', items);
        }
    });
}

// Export for external use
window.initWidgetSortable = initWidgetSortable;
