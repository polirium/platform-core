"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const alpinejs_hold_directive_1 = require("@wireui/alpinejs-hold-directive");
document.addEventListener('alpine:init', () => {
    window.Alpine.directive('hold', alpinejs_hold_directive_1.directive);
});
//# sourceMappingURL=index.js.map