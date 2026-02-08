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

    console.log(`Modal Event: ${modalAction} -> ${modalId}`);

    const modalElement = document.getElementById(modalId);

    if (!modalElement) {
        // Critical: If element missing but action is hide, we might have a ghost backdrop
        if (modalAction === 'hide') {
            cleanupGhostBackdrops();
        }
        console.warn(`Modal element not found: ${modalId}`);
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

// Robust Cleanup Function
const cleanupGhostBackdrops = () => {
    setTimeout(() => {
        const openModals = document.querySelectorAll('.modal.show');
        const backdrops = document.querySelectorAll('.modal-backdrop');

        // Logic: Keep exactly as many backdrops as visible modals
        const requiredBackdrops = openModals.length;
        const currentBackdrops = backdrops.length;

        if (currentBackdrops > requiredBackdrops) {
            console.log('Cleaning up ghost backdrops...');
            // Remove excess backdrops (from end)
            for (let i = 0; i < (currentBackdrops - requiredBackdrops); i++) {
                if (backdrops[currentBackdrops - 1 - i]) {
                    backdrops[currentBackdrops - 1 - i].remove();
                }
            }
        }

        // Deep Clean: If no modals open, ensure body is clean
        if (requiredBackdrops === 0) {
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');

            // Safety: Remove ANY remaining backdrops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }
    }, 150); // Small delay to let Bootstrap animations finish
};

// Register Listeners
Livewire.on('poli.modal', (data) => handleModalEvent(data, null));
Livewire.on('modal', (id, action) => handleModalEvent(id, action));

// Hook into Livewire lifecycle to cleanup after DOM updates
// This fixes case where Livewire removes the modal element from DOM before it hides
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Livewire !== 'undefined') {
        // morph.updated hook still works in v4
        Livewire.hook('morph.updated', ({ el, component }) => {
            // Run cleanup periodically after updates
            cleanupGhostBackdrops();
        });

        // Livewire v4: Replace 'commit' hook with 'interceptMessage'
        try {
            Livewire.interceptMessage(({ message, onSuccess }) => {
                onSuccess(() => {
                    // Queue cleanup after DOM updates from commit are processed
                    setTimeout(cleanupGhostBackdrops, 10);
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

