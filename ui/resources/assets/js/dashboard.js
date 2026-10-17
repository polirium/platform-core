/**
 * Dashboard widget grid (gridstack.js).
 *
 * The server renders every widget with its gs-x/gs-y/gs-w attributes. The grid
 * is (re)built after each dashboard render: static in view mode, draggable and
 * resizable (width) in edit mode. Heights follow the widget content. Changes
 * are sent to the dashboard component, which saves them on "Save layout".
 */

let grid = null;
let observers = [];

const GRID_ID = 'dashboard-grid';

function dashboardComponent(el) {
    const root = el.closest('[wire\\:id]');

    return root && window.Livewire ? window.Livewire.find(root.getAttribute('wire:id')) : null;
}

function destroyGrid() {
    observers.forEach((observer) => observer.disconnect());
    observers = [];

    if (grid) {
        const el = grid.el;

        // Livewire has usually re-rendered the grid already and removed the
        // handles gridstack added; gridstack then fails to remove them itself.
        try {
            grid.destroy(false);
        } catch {
            // ignore: the grid is rebuilt from the fresh markup below
        }

        if (el) {
            delete el.gridstack;
            el.querySelectorAll('.ui-resizable-handle').forEach((handle) => handle.remove());
        }

        grid = null;
    }
}

function fitToContent(item) {
    if (grid && item.gridstackNode) {
        grid.resizeToContent(item);
    }
}

function initDashboardGrid() {
    destroyGrid();

    const el = document.getElementById(GRID_ID);

    if (!el || typeof window.GridStack === 'undefined') {
        return;
    }

    const editing = el.closest('.dashboard-container')?.dataset.editMode === 'true';

    grid = window.GridStack.init({
        column: 12,
        cellHeight: 16,
        margin: 8,
        float: false,
        animate: true,
        sizeToContent: true,
        staticGrid: !editing,
        handle: '.widget-drag-handle',
        resizable: { handles: 'e,w' },
        columnOpts: { breakpoints: [{ w: 768, c: 1 }] },
    }, el);

    // Widgets load data and change height (pagination, period switch): keep
    // each grid cell as tall as its content.
    el.querySelectorAll('.grid-stack-item').forEach((item) => {
        const content = item.querySelector('.dashboard-widget');

        if (!content || typeof ResizeObserver === 'undefined') {
            return;
        }

        const observer = new ResizeObserver(() => fitToContent(item));
        observer.observe(content);
        observers.push(observer);
    });

    grid.on('change', () => {
        // The one-column phone layout is derived, never saved.
        if (!editing || grid.getColumn() !== 12) {
            return;
        }

        const items = grid.save(false).map(({ id, x, y, w, h }) => ({ id, x, y, w, h }));
        dashboardComponent(el)?.call('updateGrid', items);
    });
}

function onLivewire(callback) {
    if (window.Livewire) {
        callback();
    } else {
        document.addEventListener('livewire:init', callback, { once: true });
    }
}

onLivewire(() => {
    // Rebuild after the dashboard re-renders (edit mode, add/remove, save,
    // reset). The microtask runs once Livewire has applied the new HTML;
    // responses without HTML (grid position updates) leave the grid alone.
    window.Livewire.hook('commit', ({ component, succeed }) => {
        succeed(({ effects }) => {
            if (!effects?.html) {
                return;
            }

            queueMicrotask(() => {
                if (component.el?.querySelector(`#${GRID_ID}`)) {
                    initDashboardGrid();
                }
            });
        });
    });
});

document.addEventListener('livewire:initialized', initDashboardGrid);
document.addEventListener('livewire:navigated', initDashboardGrid);

if (document.readyState !== 'loading') {
    requestAnimationFrame(initDashboardGrid);
}

window.initDashboardGrid = initDashboardGrid;
