"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.currency = exports.defaultConfig = void 0;
const unMaskCurrency_1 = require("./unMaskCurrency");
const maskCurrency_1 = require("./maskCurrency");
exports.defaultConfig = {
    thousands: ',',
    decimal: '.',
    precision: 2
};
exports.currency = {
    mask: maskCurrency_1.maskCurrency,
    unMask: unMaskCurrency_1.unMaskCurrency
};
exports.default = exports.currency;
//# sourceMappingURL=index.js.map