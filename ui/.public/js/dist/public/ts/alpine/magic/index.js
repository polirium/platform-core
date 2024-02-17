"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const props_1 = __importDefault(require("./props"));
document.addEventListener('alpine:init', () => {
    window.Alpine.magic('props', props_1.default);
});
//# sourceMappingURL=index.js.map