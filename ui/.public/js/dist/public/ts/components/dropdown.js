"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = () => ({
    status: false,
    open() { this.status = true; },
    close() { this.status = false; },
    toggle() { this.status = !this.status; }
});
//# sourceMappingURL=dropdown.js.map