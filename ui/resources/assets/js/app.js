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


  //livewire event
Livewire.on('poli.modal', (data) => {
    const d = data[0];
    const id = d[0];
    const event = d[1] == 'hide' ? 'hide' : 'show';

    console.log(id, event);

    const modalElement = document.getElementById(id);
    if (!modalElement) {
        console.error('Modal element not found:', id);
        return;
    }

    if(event == 'show'){
        // Get existing instance or create new one
        let modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });

            // Add event listeners to fix accessibility issues
            modalElement.addEventListener('shown.bs.modal', function() {
                // Remove aria-hidden when modal is fully shown
                modalElement.removeAttribute('aria-hidden');
            });

            modalElement.addEventListener('hidden.bs.modal', function() {
                // Clean up backdrop and body classes
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                // Remove modal-open class from body if no other modals are open
                const openModals = document.querySelectorAll('.modal.show');
                if (openModals.length === 0) {
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('padding-right');
                }
            });
        }
        modalInstance.show();
    }else if(event == 'hide'){
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
    }
})
