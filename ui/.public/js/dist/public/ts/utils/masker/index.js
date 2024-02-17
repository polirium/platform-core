"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.masker = exports.applyMask = void 0;
const dynamicMasker_1 = __importDefault(require("./dynamicMasker"));
const masker_1 = __importDefault(require("./masker"));
const helpers_1 = require("../helpers");
const applyMask = (mask, value, masked = true) => {
    return Array.isArray(mask)
        ? (0, dynamicMasker_1.default)(mask, (0, helpers_1.str)(value), masked)
        : (0, masker_1.default)(mask, (0, helpers_1.str)(value), masked);
};
exports.applyMask = applyMask;
const masker = (mask, value) => {
    return {
        mask,
        value,
        getOriginal() {
            return (0, exports.applyMask)(this.mask, this.value, false);
        },
        apply(value) {
            this.value = (0, exports.applyMask)(this.mask, value);
            return this;
        }
    }.apply(value);
};
exports.masker = masker;
exports.default = exports.masker;
//# sourceMappingURL=index.js.map