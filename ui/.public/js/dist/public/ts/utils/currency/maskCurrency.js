"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.maskCurrency = void 0;
const helpers_1 = require("../helpers");
const applyCurrencyMask = (numbers, separator) => {
    return numbers.replace(/\B(?=(\d{3})+(?!\d))/g, separator);
};
const splitCurrency = (numbers, config) => {
    if (!numbers)
        return [];
    let [digits = null, decimals = null] = numbers?.split(config.decimal) ?? [];
    digits = (0, helpers_1.onlyNumbers)(digits);
    decimals = (0, helpers_1.onlyNumbers)(decimals);
    if (digits) {
        digits = parseInt(digits).toString();
    }
    return [digits, decimals];
};
const joinCurrency = (digits, decimals, config, walkDecimals = true) => {
    if (digits && config.precision === 0) {
        return applyCurrencyMask(digits, config.thousands);
    }
    if (!walkDecimals && decimals) {
        decimals = decimals?.slice(0, config.precision);
    }
    if (walkDecimals && decimals && config.precision && decimals?.length > config.precision) {
        digits += decimals.slice(0, decimals.length - config.precision);
        decimals = decimals.slice(-Math.abs(config.precision));
    }
    if (digits) {
        digits = applyCurrencyMask(digits, config.thousands);
    }
    if (!decimals) {
        return digits;
    }
    return `${digits}${config.decimal}${decimals}`;
};
const maskCurrency = (value = null, config, walkDecimals = true) => {
    if (typeof value === 'number') {
        value = value.toString().replace('.', config.decimal);
    }
    const [digits = null, decimals = null] = splitCurrency(value, config);
    let currency = digits;
    if (value?.startsWith('-')) {
        currency = `-${currency}`;
    }
    currency = joinCurrency(currency, decimals, config, walkDecimals);
    return currency;
};
exports.maskCurrency = maskCurrency;
//# sourceMappingURL=maskCurrency.js.map