"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = () => ({
    status: false,
    get type() {
        return this.status ? 'text' : 'password';
    },
    toggle() {
        this.status = !this.status;
    }
});
//# sourceMappingURL=password.js.map