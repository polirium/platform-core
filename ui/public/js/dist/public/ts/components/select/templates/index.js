"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.templates = void 0;
const option_1 = __importDefault(require("./option"));
const userOption_1 = __importDefault(require("./userOption"));
exports.templates = {
    'default': option_1.default,
    'user-option': userOption_1.default
};
exports.default = exports.templates;
//# sourceMappingURL=index.js.map