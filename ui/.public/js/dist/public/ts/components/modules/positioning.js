"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.positioning = void 0;
const alpine_1 = require("../alpine");
const dom_1 = require("@floating-ui/dom");
exports.positioning = {
    ...alpine_1.baseComponent,
    popover: false,
    $refs: {},
    cleanupPosition: null,
    initPositioningSystem() {
        this.$watch('popover', (state) => {
            if (!state && this.cleanupPosition) {
                this.cleanupPosition();
                this.cleanupPosition = null;
            }
            if (window.innerWidth < 640) {
                return this.$refs.popover.removeAttribute('style');
            }
            if (state && !this.cleanupPosition) {
                this.$nextTick(() => this.syncPopoverPosition());
            }
        });
    },
    syncPopoverPosition() {
        this.cleanupPosition = (0, dom_1.autoUpdate)(this.$root, this.$refs.popover, () => this.updatePosition());
    },
    open() { this.popover = true; },
    close() { this.popover = false; },
    toggle() { this.popover = !this.popover; },
    handleEscape() { this.close(); },
    updatePosition() {
        (0, dom_1.computePosition)(this.$root, this.$refs.popover, {
            placement: 'bottom',
            middleware: [
                (0, dom_1.offset)(4),
                (0, dom_1.flip)()
            ]
        }).then(({ x, y }) => {
            return Object.assign(this.$refs.popover.style, {
                position: 'absolute',
                left: `${x}px`,
                top: `${y}px`
            });
        });
    }
};
//# sourceMappingURL=positioning.js.map