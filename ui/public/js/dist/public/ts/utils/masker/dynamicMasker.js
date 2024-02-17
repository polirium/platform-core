"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const masker_1 = __importDefault(require("./masker"));
exports.default = (masks, value, masked = true) => {
    masks = masks.sort((a, b) => a.length - b.length);
    let i = 0;
    while (i < masks.length) {
        const currentMask = masks[i];
        i++;
        const nextMask = masks[i];
        if (!(nextMask && ((0, masker_1.default)(nextMask, value, true)?.length ?? 0) > currentMask.length)) {
            return (0, masker_1.default)(currentMask, value, masked);
        }
    }
    return '';
};
//# sourceMappingURL=dynamicMasker.js.map