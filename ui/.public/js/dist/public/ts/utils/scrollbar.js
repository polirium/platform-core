"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.toggleScrollbar = void 0;
const toggleScrollbar = (state) => {
    const elements = [...document.querySelectorAll('body, [main-container]')];
    state
        ? elements.forEach(el => el.classList.add('!overflow-hidden'))
        : elements.forEach(el => el.classList.remove('!overflow-hidden'));
};
exports.toggleScrollbar = toggleScrollbar;
exports.default = exports.toggleScrollbar;
//# sourceMappingURL=scrollbar.js.map