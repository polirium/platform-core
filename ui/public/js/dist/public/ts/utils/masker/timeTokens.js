"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.minutesToken = exports.hour12Token = exports.hour24Token = void 0;
const helpers_1 = require("../helpers");
const getOutput = (value, iValue, pattern) => {
    const digits = (0, helpers_1.onlyNumbers)(value.slice(iValue, iValue + 2));
    if (digits.length === 2 && pattern?.test(digits)) {
        return digits;
    }
    return value[iValue];
};
exports.hour24Token = {
    pattern: /([01][0-9])|(2[0-3])/,
    validate(value, iValue) {
        const hours = (0, helpers_1.onlyNumbers)(value.slice(iValue, iValue + 2));
        if (hours.length === 2 && this.pattern?.test(hours)) {
            return true;
        }
        return /[0-2]/.test(hours);
    },
    output(value, iValue) {
        return getOutput(value, iValue, this.pattern);
    }
};
exports.hour12Token = {
    pattern: /[1-9]|1[0-2]/,
    validate(value, iValue) {
        const hours = (0, helpers_1.onlyNumbers)(value.slice(iValue, iValue + 2));
        if (hours.length === 2) {
            return /1[0-2]/.test(hours);
        }
        return /[1-9]/.test(hours);
    },
    output(value, iValue) {
        return getOutput(value, iValue, this.pattern);
    }
};
exports.minutesToken = {
    pattern: /[0-5][0-9]/,
    validate(value, iValue) {
        const minutes = (0, helpers_1.onlyNumbers)(value.slice(iValue, iValue + 2));
        if (/[0-5]/.test(minutes[0]) && /[0-9]/.test(minutes[1])) {
            return Boolean(this.pattern?.test(minutes));
        }
        return /[0-5]/.test(value[iValue]);
    },
    output(value, iValue) {
        return getOutput(value, iValue, this.pattern);
    }
};
//# sourceMappingURL=timeTokens.js.map