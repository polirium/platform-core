/*!
 * Tabler Demo v1.0.0 (https://tabler.io)
 * Copyright 2018-2025 The Tabler Authors
 * Copyright 2018-2025 codecalm.net Paweł Kuna
 * Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
 */
// Setting items
const items = {
    "menu-position": {
      localStorage: "tablerMenuPosition",
      default: "top"
    },
    "menu-behavior": {
      localStorage: "tablerMenuBehavior",
      default: "sticky"
    },
    "container-layout": {
      localStorage: "tablerContainerLayout",
      default: "boxed"
    }
  };

  // Theme config
  const config = {};
  for (const [key, params] of Object.entries(items)) {
    const lsParams = localStorage.getItem(params.localStorage);
    config[key] = lsParams ? lsParams : params.default;
  }

  // Parse url params
  const parseUrl = () => {
    const search = window.location.search.substring(1);
    const params = search.split("&");
    for (let i = 0; i < params.length; i++) {
      const arr = params[i].split("=");
      const key = arr[0];
      const value = arr[1];
      if (!!items[key]) {
        // Save to localStorage
        localStorage.setItem(items[key].localStorage, value);

        // Update local variables
        config[key] = value;
      }
    }
  };

  // Toggle form controls
  const toggleFormControls = form => {
    for (const [key, params] of Object.entries(items)) {
      const elem = form.querySelector(`[name="settings-${key}"][value="${config[key]}"]`);
      if (elem) {
        elem.checked = true;
      }
    }
  };

  // Submit form
  const submitForm = form => {
    // Save data to localStorage
    for (const [key, params] of Object.entries(items)) {
      // Save to localStorage
      const value = form.querySelector(`[name="settings-${key}"]:checked`).value;
      localStorage.setItem(params.localStorage, value);

      // Update local variables
      config[key] = value;
    }
    window.dispatchEvent(new Event("resize"));
    new bootstrap.Offcanvas(form).hide();
  };

  // Parse url
  parseUrl();

  // Elements
  const form = document.querySelector("#offcanvasSettings");

  // Toggle form controls
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      submitForm(form);
    });
    toggleFormControls(form);
  }
  //# sourceMappingURL=demo.js.map


// Unified Modal Handler
const handleModalEvent = (id, action) => {
    // Determine ID and Action
    let modalId = id;
    let modalAction = action || 'show';

    // Handle array format often sent by Livewire
    if (Array.isArray(id)) {
        modalId = id[0];
        modalAction = id[1] || 'show';
    }

    // Normalize action
    modalAction = modalAction === 'hide' ? 'hide' : 'show';

    // console.log(`Modal Event: ${modalAction} -> ${modalId}`);

    const modalElement = document.getElementById(modalId);

    if (!modalElement) {
        // Critical: If element missing but action is hide, we might have a ghost backdrop
        if (modalAction === 'hide') {
            cleanupGhostBackdrops();
        }
        // console.warn(`Modal element not found: ${modalId}`);
        return;
    }

    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement, {
        backdrop: true,
        keyboard: true,
        focus: true
    });

    if (modalAction === 'show') {
        modalInstance.show();
    } else {
        modalInstance.hide();
    }
};

// Optimized Cleanup Function - only clean when necessary
let cleanupTimeout = null;
const cleanupGhostBackdrops = () => {
    // Clear previous timeout to debounce
    if (cleanupTimeout) {
        clearTimeout(cleanupTimeout);
    }

    cleanupTimeout = setTimeout(() => {
        const openModals = document.querySelectorAll('.modal.show');
        const backdrops = document.querySelectorAll('.modal-backdrop');

        const requiredBackdrops = openModals.length;
        const currentBackdrops = backdrops.length;

        // Only cleanup if there are EXCESS backdrops (more than modals)
        if (currentBackdrops > requiredBackdrops && currentBackdrops > 1) {
            // Remove excess backdrops (from end)
            for (let i = 0; i < (currentBackdrops - requiredBackdrops); i++) {
                if (backdrops[currentBackdrops - 1 - i]) {
                    backdrops[currentBackdrops - 1 - i].remove();
                }
            }
        }

        // Deep Clean: If no modals open, ensure body is clean
        if (requiredBackdrops === 0 && currentBackdrops > 0) {
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');

            // Safety: Remove ANY remaining backdrops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }

        cleanupTimeout = null;
    }, 50); // Reduced from 150ms to 50ms for faster response
};

// Register Listeners
Livewire.on('poli.modal', (data) => {
    // data is array: ['modal-id', 'action']
    handleModalEvent(data[0], data[1]);
});
Livewire.on('modal', (id, action) => handleModalEvent(id, action));

// Hook into Livewire lifecycle to cleanup after DOM updates
// This fixes case where Livewire removes the modal element from DOM before it hides
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Livewire !== 'undefined') {
        // REMOVED: morph.updated cleanup to reduce lag
        // Cleanup is now handled by hidden.bs.modal event and explicit calls

        // Livewire v4: interceptMessage for cleanup after actions
        // ONLY cleanup when necessary to avoid lag
        try {
            Livewire.interceptMessage(({ message, onSuccess }) => {
                onSuccess(() => {
                    // Only cleanup if there are ghost backdrops (more backdrops than modals)
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    const modals = document.querySelectorAll('.modal.show');
                    if (backdrops.length > modals.length) {
                        cleanupGhostBackdrops();
                    }
                });
            });
        } catch (e) {
            console.warn('Livewire interceptMessage registration failed', e);
        }
    }
});

// Global Cleanup on Hidden (Bootstrap Event) - Delegation
document.addEventListener('hidden.bs.modal', function (event) {
    cleanupGhostBackdrops();
});

// Immediate Feedback for Modal Buttons
// This provides instant visual feedback before Livewire processes the event
document.addEventListener('click', function(event) {
    const button = event.target.closest('[data-action="show-modal"]');
    if (button) {
        const modalId = button.getAttribute('data-modal');
        const spinner = button.querySelector('.spinner-border');
        const btnText = button.querySelector('.btn-text');

        // Show loading state immediately
        if (spinner) spinner.classList.remove('d-none');
        if (btnText) btnText.style.opacity = '0.5';
        button.disabled = true;

        // Listen for modal to be shown, then reset button
        const resetButton = () => {
            if (spinner) spinner.classList.add('d-none');
            if (btnText) btnText.style.opacity = '1';
            button.disabled = false;
        };

        // Reset after modal shows OR after timeout (fallback)
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            modalElement.addEventListener('shown.bs.modal', resetButton, { once: true });
        }

        // Fallback: reset after 3 seconds if modal never shows
        setTimeout(resetButton, 3000);

        // Dispatch Livewire event (keep original event name format)
        Livewire.dispatch('show-modal-create-' + (modalId.replace('modal-', '') === 'modal-user' ? 'user' : modalId.replace('modal-', '')));

        // Also try generic format
        setTimeout(() => {
            if (button.disabled && !document.getElementById(modalId).classList.contains('show')) {
                // Fallback: try direct modal show if Livewire event didn't work
                Livewire.dispatch('modal', modalId, 'show');
            }
        }, 100);
    }
}, true); // Use capture to catch clicks before other handlers


