"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.focusables = void 0;
const alpine_1 = require("../alpine");
exports.focusables = {
    ...alpine_1.baseComponent,
    focusableSelector: '',
    getFocusables() {
        return Array.from(this.$root.querySelectorAll(this.focusableSelector))
            .filter(el => !el.hasAttribute('disabled'));
    },
    getFirstFocusable() { return this.getFocusables().shift(); },
    getLastFocusable() { return this.getFocusables().pop(); },
    getNextFocusable() { return this.getFocusables()[this.getNextFocusableIndex()] || this.getFirstFocusable(); },
    getPrevFocusable() { return this.getFocusables()[this.getPrevFocusableIndex()] || this.getLastFocusable(); },
    getNextFocusableIndex() {
        if (document.activeElement instanceof HTMLElement) {
            return (this.getFocusables().indexOf(document.activeElement) + 1) % (this.getFocusables().length + 1);
        }
        return 0;
    },
    getPrevFocusableIndex() {
        if (document.activeElement instanceof HTMLElement) {
            return Math.max(0, this.getFocusables().indexOf(document.activeElement)) - 1;
        }
        return 0;
    }
};
//# sourceMappingURL=focusables.js.map