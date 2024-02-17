"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.unMaskCurrency = void 0;
const unMaskCurrency = (value, config) => {
    if (!value)
        return null;
    const currency = parseFloat(value.replaceAll(config.thousands, '').replace(config.decimal, '.'));
    const isNegative = value.startsWith('-');
    return isNegative ? -Math.abs(currency) : Math.abs(currency);
};
exports.unMaskCurrency = unMaskCurrency;
//# sourceMappingURL=unMaskCurrency.js.map