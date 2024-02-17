"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const alpine_1 = require("../alpine");
const currency_1 = __importDefault(require("@/utils/currency"));
const helpers_1 = require("@/utils/helpers");
exports.default = (options) => ({
    ...alpine_1.baseComponent,
    model: options.model,
    input: null,
    config: {
        emitFormatted: options.emitFormatted,
        isBlur: options.isBlur,
        thousands: options.thousands,
        decimal: options.decimal,
        precision: options.precision
    },
    init() {
        if (typeof this.model !== 'object') {
            this.input = currency_1.default.mask(this.model, this.config, false);
        }
        this.$watch('model', value => {
            if (!this.config.emitFormatted) {
                value = currency_1.default.mask(value, this.config, false);
            }
            if (value !== this.input) {
                this.mask(value, false, false);
            }
        });
    },
    mask(value, emitInput = true, walkDecimals = true) {
        if (typeof value === 'string'
            && value.endsWith(this.config.decimal)
            && (0, helpers_1.occurrenceCount)(value, this.config.decimal) === 1) {
            if (value.length === 1) {
                return (this.input = `0${this.config.decimal}`);
            }
            return;
        }
        this.input = currency_1.default.mask(value, this.config, walkDecimals);
        if (!this.config.isBlur && emitInput) {
            this.emitInput(this.input);
        }
    },
    unMask(value) {
        return currency_1.default.unMask(value, this.config);
    },
    emitInput(value) {
        this.model = this.config.emitFormatted
            ? value
            : this.unMask(value);
    }
});
//# sourceMappingURL=currency.js.map