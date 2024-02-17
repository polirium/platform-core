"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.tokens = void 0;
const timeTokens_1 = require("./timeTokens");
exports.tokens = {
    '#': { pattern: /\d/ },
    'X': { pattern: /[0-9a-zA-Z]/ },
    'S': { pattern: /[a-zA-Z]/ },
    'A': { pattern: /[a-zA-Z]/, transform: (v) => v.toLocaleUpperCase() },
    'a': { pattern: /[a-zA-Z]/, transform: (v) => v.toLocaleLowerCase() },
    '!': { escape: true },
    'H': timeTokens_1.hour24Token,
    'h': timeTokens_1.hour12Token,
    'm': timeTokens_1.minutesToken
};
exports.default = exports.tokens;
//# sourceMappingURL=tokens.js.map