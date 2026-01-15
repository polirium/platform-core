/**
 * Polirium Keep-Alive System
 *
 * Prevents session timeout and CSRF token expiration for POS/critical pages.
 * Features:
 * - Periodic heartbeat to keep session alive
 * - Auto-refresh CSRF token
 * - Livewire connection monitoring and auto-reconnect
 * - Graceful session expiration handling
 */

class PoliriumKeepAlive {
    constructor(options = {}) {
        // Configuration
        this.heartbeatInterval = options.heartbeatInterval || 5 * 60 * 1000; // 5 minutes
        this.csrfRefreshInterval = options.csrfRefreshInterval || 30 * 60 * 1000; // 30 minutes
        this.maxRetries = options.maxRetries || 3;
        this.retryDelay = options.retryDelay || 5000; // 5 seconds
        this.onSessionExpired = options.onSessionExpired || this.defaultSessionExpiredHandler;
        this.onReconnected = options.onReconnected || (() => {});
        this.debug = options.debug || false;

        // State
        this.heartbeatTimer = null;
        this.csrfRefreshTimer = null;
        this.retryCount = 0;
        this.isRunning = false;
        this.lastHeartbeat = null;
        this.connectionLost = false;
        this.csrfHookSetup = false; // Flag to ensure CSRF hook is only registered once

        // Bind methods
        this.heartbeat = this.heartbeat.bind(this);
        this.refreshCsrfToken = this.refreshCsrfToken.bind(this);
        this.handleVisibilityChange = this.handleVisibilityChange.bind(this);
        this.handleOnline = this.handleOnline.bind(this);
        this.handleLivewireError = this.handleLivewireError.bind(this);
    }

    /**
     * Start the keep-alive system
     */
    start() {
        if (this.isRunning) {
            this.log('Keep-alive already running');
            return;
        }

        this.log('Starting keep-alive system');
        this.isRunning = true;

        // Initial heartbeat
        this.heartbeat();

        // Set up periodic heartbeat
        this.heartbeatTimer = setInterval(this.heartbeat, this.heartbeatInterval);

        // Set up CSRF refresh (less frequent)
        this.csrfRefreshTimer = setInterval(this.refreshCsrfToken, this.csrfRefreshInterval);

        // Listen for visibility changes (tab focus/blur)
        document.addEventListener('visibilitychange', this.handleVisibilityChange);

        // Listen for online/offline events
        window.addEventListener('online', this.handleOnline);
        window.addEventListener('offline', () => {
            this.log('Browser went offline');
            this.connectionLost = true;
        });

        // Monitor Livewire errors
        this.setupLivewireMonitoring();

        // Setup Livewire CSRF token injection hook
        this.setupLivewireCsrfHook();

        this.log('Keep-alive system started');
    }

    /**
     * Stop the keep-alive system
     */
    stop() {
        if (!this.isRunning) return;

        this.log('Stopping keep-alive system');
        this.isRunning = false;

        clearInterval(this.heartbeatTimer);
        clearInterval(this.csrfRefreshTimer);

        document.removeEventListener('visibilitychange', this.handleVisibilityChange);
        window.removeEventListener('online', this.handleOnline);

        this.heartbeatTimer = null;
        this.csrfRefreshTimer = null;
    }

    /**
     * Send heartbeat to server
     */
    async heartbeat() {
        if (!this.isRunning) return;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const response = await fetch('/admin/heartbeat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            const data = await response.json();

            if (response.ok && data.status === 'ok') {
                this.log('Heartbeat successful', data);
                this.lastHeartbeat = new Date();
                this.retryCount = 0;

                // Update CSRF token if returned
                if (data.csrf_token) {
                    this.updateCsrfToken(data.csrf_token);
                }

                // Handle reconnection after connection loss
                if (this.connectionLost) {
                    this.connectionLost = false;
                    this.onReconnected();
                    this.showNotification('Kết nối đã được khôi phục', 'success');
                }

            } else if (response.status === 401 || data.status === 'unauthenticated') {
                this.log('Session expired');
                this.handleSessionExpired();
            } else {
                throw new Error(`Heartbeat failed: ${data.status || response.status}`);
            }

        } catch (error) {
            this.log('Heartbeat error:', error);
            this.handleHeartbeatError(error);
        }
    }

    /**
     * Refresh CSRF token only (lightweight)
     */
    async refreshCsrfToken() {
        if (!this.isRunning) return;

        try {
            const response = await fetch('/admin/csrf-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            const data = await response.json();

            if (data.csrf_token) {
                this.updateCsrfToken(data.csrf_token);
                this.log('CSRF token refreshed');
            }

        } catch (error) {
            this.log('CSRF refresh error:', error);
        }
    }

    /**
     * Update CSRF token in DOM and Livewire
     */
    updateCsrfToken(token) {
        // Update meta tag (Livewire will read from here via our hook)
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.content = token;
        }

        // Update any hidden CSRF inputs
        document.querySelectorAll('input[name="_token"]').forEach(input => {
            input.value = token;
        });

        this.log('CSRF token updated');
    }

    /**
     * Setup Livewire CSRF token injection hook
     * This hook runs on every Livewire request and injects the current CSRF token from meta tag
     */
    setupLivewireCsrfHook() {
        if (this.csrfHookSetup) {
            this.log('CSRF hook already setup, skipping');
            return;
        }

        if (!window.Livewire || typeof window.Livewire.hook !== 'function') {
            this.log('Livewire hook not available, skipping CSRF hook setup');
            return;
        }

        // Register hook ONCE to inject CSRF token on every request
        window.Livewire.hook('request', ({ options }) => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (csrfToken) {
                // Ensure headers object exists
                if (!options.headers) {
                    options.headers = {};
                }
                // Inject the current CSRF token from meta tag
                options.headers['X-CSRF-TOKEN'] = csrfToken;
            }
        });

        this.csrfHookSetup = true;
        this.log('Livewire CSRF hook setup complete');
    }

    /**
     * Handle heartbeat errors with retry logic
     */
    handleHeartbeatError(error) {
        this.retryCount++;
        this.connectionLost = true;

        if (this.retryCount <= this.maxRetries) {
            this.log(`Retrying heartbeat (${this.retryCount}/${this.maxRetries})...`);
            setTimeout(this.heartbeat, this.retryDelay * this.retryCount);
        } else {
            this.log('Max retries reached, showing warning');
            this.showConnectionWarning();
        }
    }

    /**
     * Handle session expiration
     */
    handleSessionExpired() {
        this.stop();
        this.onSessionExpired();
    }

    /**
     * Default session expired handler
     */
    defaultSessionExpiredHandler() {
        // Show modal or notification
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Phiên làm việc đã hết hạn',
                text: 'Vui lòng đăng nhập lại để tiếp tục.',
                icon: 'warning',
                confirmButtonText: 'Đăng nhập lại',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then(() => {
                window.location.href = '/admin/login';
            });
        } else {
            if (confirm('Phiên làm việc đã hết hạn. Bấm OK để đăng nhập lại.')) {
                window.location.href = '/admin/login';
            }
        }
    }

    /**
     * Handle visibility change (tab focus)
     */
    handleVisibilityChange() {
        if (document.visibilityState === 'visible') {
            this.log('Tab became visible, sending heartbeat');
            // Immediate heartbeat when tab becomes visible
            this.heartbeat();
        }
    }

    /**
     * Handle online event
     */
    handleOnline() {
        this.log('Browser came online, sending heartbeat');
        this.retryCount = 0;
        this.heartbeat();
    }

    /**
     * Setup Livewire error monitoring
     */
    setupLivewireMonitoring() {
        if (!window.Livewire) {
            this.log('Livewire not found, skipping Livewire monitoring');
            return;
        }

        // Livewire 3.x hooks
        if (window.Livewire.hook) {
            window.Livewire.hook('request', ({ fail }) => {
                fail(({ status, content }) => {
                    this.handleLivewireError({ status, content });
                });
            });
        }

        // Listen for Livewire errors
        window.addEventListener('livewire:error', (event) => {
            this.handleLivewireError(event.detail);
        });
    }

    /**
     * Handle Livewire request errors
     */
    handleLivewireError(error) {
        this.log('Livewire error:', error);

        if (error.status === 419) {
            // CSRF token mismatch - refresh token and retry
            this.log('CSRF mismatch detected, refreshing token');
            this.refreshCsrfToken().then(() => {
                this.showNotification('Token đã được cập nhật. Vui lòng thử lại.', 'info');
            });
        } else if (error.status === 401) {
            // Unauthorized - session expired
            this.handleSessionExpired();
        } else if (error.status === 0 || error.status === 503) {
            // Network error or server unavailable
            this.showConnectionWarning();
        }
    }

    /**
     * Show connection warning notification
     */
    showConnectionWarning() {
        this.showNotification(
            'Mất kết nối với máy chủ. Đang thử kết nối lại...',
            'warning'
        );
    }

    /**
     * Show notification (toast)
     */
    showNotification(message, type = 'info') {
        // Try to use existing toast system
        if (window.Livewire) {
            window.Livewire.dispatch('toast', { message, type });
        }

        // Fallback to console
        this.log(`[${type.toUpperCase()}] ${message}`);

        // Try Bootstrap toast if available
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const toastContainer = document.getElementById('toast-container') || this.createToastContainer();
            const toastHtml = `
                <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'primary'} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastEl = toastContainer.lastElementChild;
            const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }
    }

    /**
     * Create toast container if not exists
     */
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }

    /**
     * Debug logging
     */
    log(...args) {
        if (this.debug) {
            console.log('[KeepAlive]', ...args);
        }
    }

    /**
     * Get status info
     */
    getStatus() {
        return {
            isRunning: this.isRunning,
            lastHeartbeat: this.lastHeartbeat,
            connectionLost: this.connectionLost,
            retryCount: this.retryCount,
        };
    }
}

// Auto-export for global access
window.PoliriumKeepAlive = PoliriumKeepAlive;

// Export for module systems
export default PoliriumKeepAlive;
export { PoliriumKeepAlive };
