"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const lodash_kebabcase_1 = __importDefault(require("lodash.kebabcase"));
window.$openModal = name => {
    return window.dispatchEvent(new Event(`open-wireui-modal:${(0, lodash_kebabcase_1.default)(name)}`));
};
//# sourceMappingURL=modal.js.map