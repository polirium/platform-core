/**
 * Media Manager JavaScript
 * Handles Image Editor with Cropper.js
 */

import Cropper from 'cropperjs';

// Make Cropper available globally for Alpine.js
window.Cropper = Cropper;

/**
 * Image Editor Alpine.js Component
 * Usage: x-data="imageEditor(mediaId, imageUrl)"
 */
window.imageEditor = function(mediaId, imageUrl) {
    return {
        mediaId: mediaId,
        imageUrl: imageUrl,
        cropper: null,
        loading: false,
        cropData: { x: 0, y: 0, width: 0, height: 0 },
        message: '',
        messageType: 'success',

        init() {
            this.$nextTick(() => {
                this.initCropper();
            });
        },

        initCropper() {
            const img = this.$refs.cropperImage;
            if (!img) {
                console.error('Cropper image element not found');
                return;
            }

            // Wait for image to load
            if (!img.complete) {
                img.onload = () => this.createCropper(img);
            } else {
                this.createCropper(img);
            }
        },

        createCropper(img) {
            if (this.cropper) {
                this.cropper.destroy();
            }

            this.cropper = new Cropper(img, {
                viewMode: 1,
                dragMode: 'crop',
                aspectRatio: NaN,
                autoCropArea: 0.8,
                restore: false,
                guides: true,
                center: true,
                highlight: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                ready: () => {
                    console.log('Cropper initialized successfully');
                },
                crop: (event) => {
                    this.cropData = {
                        x: Math.round(event.detail.x),
                        y: Math.round(event.detail.y),
                        width: Math.round(event.detail.width),
                        height: Math.round(event.detail.height)
                    };
                }
            });
        },

        setAspectRatio(ratio) {
            if (this.cropper) {
                this.cropper.setAspectRatio(ratio === 0 ? NaN : ratio);
            }
        },

        resetCrop() {
            if (this.cropper) {
                this.cropper.reset();
            }
        },

        async applyCrop() {
            if (!this.cropper || this.loading) return;

            this.loading = true;
            this.message = '';

            const data = this.cropper.getData(true);

            try {
                // Call Livewire method
                const result = await this.$wire.cropImage(data.x, data.y, data.width, data.height);

                this.message = result.message;
                this.messageType = result.success ? 'success' : 'error';

                if (result.success) {
                    // Refresh the image
                    this.imageUrl = this.imageUrl.split('?')[0] + '?t=' + Date.now();
                    this.$nextTick(() => {
                        this.cropper.destroy();
                        setTimeout(() => this.initCropper(), 300);
                    });
                }
            } catch (error) {
                this.message = 'Error: ' + error.message;
                this.messageType = 'error';
            }

            this.loading = false;
        },

        destroy() {
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
        }
    };
};

/**
 * Context Menu Alpine.js Component
 */
window.contextMenuHandler = function() {
    return {
        show: false,
        x: 0,
        y: 0,
        itemId: null,
        itemType: null,

        open(event, itemId, itemType) {
            event.preventDefault();
            this.itemId = itemId;
            this.itemType = itemType;
            this.x = event.clientX;
            this.y = event.clientY;
            this.show = true;
        },

        close() {
            this.show = false;
        }
    };
};

// Auto-close context menu on click outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.context-menu')) {
        document.querySelectorAll('.context-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

console.log('Media Manager JS loaded');
