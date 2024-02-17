"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const modal_1 = __importDefault(require("./modal"));
document.addEventListener('alpine:init', () => {
    window.Alpine.store('wireui:modal', modal_1.default);
});
//# sourceMappingURL=index.js.map