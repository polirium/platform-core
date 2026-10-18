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

    // The server has filled the content of a modal opened on click.
    clearTimeout(modalElement._polLoadingTimer);
    modalElement.classList.remove('is-loading');

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
            Livewire.interceptMessage(({ message, onSuccess, onError, onFailure }) => {
                // A modal opened on click whose request failed (no permission,
                // server error, offline) closes instead of spinning.
                const closeLoadingModals = () => document.querySelectorAll('.modal.is-loading').forEach((el) => {
                    el.classList.remove('is-loading');
                    clearTimeout(el._polLoadingTimer);
                    const modal = bootstrap.Modal.getInstance(el);
                    if (! modal) {
                        return;
                    }
                    // Bootstrap ignores hide() while the modal is still fading in:
                    // close it once it is shown.
                    const closeWhenShown = () => modal.hide();
                    el.addEventListener('shown.bs.modal', closeWhenShown, { once: true });
                    el.addEventListener('hidden.bs.modal', () => el.removeEventListener('shown.bs.modal', closeWhenShown), { once: true });
                    modal.hide();
                });
                onError?.(closeLoadingModals);
                onFailure?.(closeLoadingModals);

                onSuccess(() => {
                    // The modal's own component answered: its content is up to date,
                    // whichever event it uses to open itself.
                    const componentEl = message.component?.el;
                    if (componentEl) {
                        document.querySelectorAll('.modal.is-loading').forEach((el) => {
                            if (el.closest('[wire\\:id]') === componentEl || el.contains(componentEl)) {
                                clearTimeout(el._polLoadingTimer);
                                el.classList.remove('is-loading');
                            }
                        });
                    }

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

// Buttons that ask the server to open a modal ($dispatch('show-modal-X') or 'modal-X').
// When the page already holds #modal-X, open it at once in a loading state;
// the server's "modal" event fills it and clears the state, so the wait for
// the round trip happens inside an open dialog instead of after the click.
// Otherwise the button gets a busy state and ignores repeat clicks.
const openingButtons = new Set();
const clearOpening = () => {
    openingButtons.forEach((button) => button.classList.remove('is-opening-modal'));
    openingButtons.clear();
};
document.addEventListener('click', (event) => {
    const button = event.target.closest('button, a');
    // wire:click="$dispatch('show-modal-X', …)", onclick="Livewire.dispatch('show-modal-X')", Alpine clicks.
    const action = ['wire:click', 'onclick', '@click', 'x-on:click']
        .map((name) => button?.getAttribute(name) || '')
        .join(' ');
    // Events are "show-modal-X" or, for a few, "modal-X" itself; both open #modal-X.
    const match = action.match(/(?:\$dispatch|Livewire\.dispatch)\(\s*['"]((?:show-)?(modal-[\w-]+))['"]/);
    if (! button || ! match) {
        return;
    }
    if (button.classList.contains('is-opening-modal')) {
        event.preventDefault();
        event.stopImmediatePropagation();
        return;
    }

    // #modal-X, or a modal that names this event in data-open-on (x-ui::modal open-on).
    const modalElement = document.getElementById(match[2])
        || document.querySelector(`.modal[data-open-on~="${match[1]}"]`);
    if (modalElement && typeof bootstrap !== 'undefined') {
        modalElement.classList.add('is-loading');
        clearTimeout(modalElement._polLoadingTimer);
        // Never leave the dialog locked if the server does not answer.
        modalElement._polLoadingTimer = setTimeout(() => modalElement.classList.remove('is-loading'), 8000);
        bootstrap.Modal.getOrCreateInstance(modalElement).show();
        return;
    }

    button.classList.add('is-opening-modal');
    openingButtons.add(button);
    setTimeout(clearOpening, 4000);
}, true);
document.addEventListener('shown.bs.modal', clearOpening);
document.addEventListener('hidden.bs.modal', (event) => {
    // A late "hidden" from an earlier close must not unlock a reopened modal.
    if (event.target.classList.contains('show')) {
        return;
    }
    clearTimeout(event.target._polLoadingTimer);
    event.target.classList.remove('is-loading');
});

// Buttons marked data-action="show-modal" data-modal="modal-X" (users screen):
// open the modal at once in its loading state and ask the server for its content.
document.addEventListener('click', function(event) {
    const button = event.target.closest('[data-action="show-modal"]');
    if (! button) {
        return;
    }
    const modalId = button.getAttribute('data-modal');
    const modalElement = modalId && document.getElementById(modalId);
    if (modalElement && typeof bootstrap !== 'undefined') {
        modalElement.classList.add('is-loading');
        clearTimeout(modalElement._polLoadingTimer);
        modalElement._polLoadingTimer = setTimeout(() => modalElement.classList.remove('is-loading'), 8000);
        bootstrap.Modal.getOrCreateInstance(modalElement).show();
    }

    // Keep original event name format: modal-user -> show-modal-create-user
    Livewire.dispatch('show-modal-create-' + modalId.replace('modal-', ''));
}, true); // Use capture to catch clicks before other handlers


