"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.jsonParse = exports.occurrenceCount = exports.onlyNumbers = exports.str = void 0;
const str = (value) => {
    return value ? value.toString() : '';
};
exports.str = str;
const onlyNumbers = (value) => {
    return (0, exports.str)(value).replace(/\D+/g, '');
};
exports.onlyNumbers = onlyNumbers;
const occurrenceCount = (haystack, needle) => {
    const regex = new RegExp(`\\${needle}`, 'g');
    return (haystack?.match(regex) || []).length;
};
exports.occurrenceCount = occurrenceCount;
const jsonParse = (value, fallback = null) => {
    try {
        return JSON.parse(value ?? '');
    }
    catch (error) {
        return fallback;
    }
};
exports.jsonParse = jsonParse;
//# sourceMappingURL=helpers.js.map