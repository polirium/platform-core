"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const focusables_1 = require("@/components/modules/focusables");
const scrollbar_1 = __importDefault(require("@/utils/scrollbar"));
const uuid_1 = __importDefault(require("@/utils/uuid"));
exports.default = (options) => ({
    ...focusables_1.focusables,
    focusableSelector: 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\']), [contenteditable]',
    show: options.model || options.show,
    id: (0, uuid_1.default)(),
    store: window.Alpine.store('wireui:modal'),
    init() {
        this.$watch('show', value => {
            if (value) {
                this.store.setCurrent(this.id);
                this.toggleScroll();
            }
            else {
                this.toggleScroll();
                this.store.remove(this.id);
            }
            this.$el.dispatchEvent(new Event(value ? 'open' : 'close'));
        });
    },
    close() { this.show = false; },
    open() { this.show = true; },
    toggleScroll() {
        if (!this.store.isFirstest(this.id))
            return;
        (0, scrollbar_1.default)(this.show);
    },
    getFocusables() {
        return Array.from(this.$root.querySelectorAll(this.focusableSelector))
            .filter(el => {
            return !el.hasAttribute('disabled')
                && !el.hasAttribute('hidden')
                && this.$root.isSameNode(el.closest('[wireui-modal]'));
        });
    },
    handleEscape() {
        if (this.store.isCurrent(this.id)) {
            this.close();
        }
    },
    handleTab(event) {
        if (this.store.isCurrent(this.id) && !event.shiftKey) {
            this.getNextFocusable().focus();
        }
    },
    handleShiftTab() {
        if (this.store.isCurrent(this.id)) {
            this.getPrevFocusable().focus();
        }
    }
});
//# sourceMappingURL=modal.js.map