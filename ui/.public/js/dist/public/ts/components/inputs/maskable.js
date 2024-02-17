"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const masker_1 = require("../../utils/masker");
exports.default = (options) => ({
    model: options.model,
    input: null,
    masker: (0, masker_1.masker)(options.mask, null),
    config: {
        emitFormatted: options.emitFormatted,
        isBlur: options.isBlur,
        mask: options.mask
    },
    init() {
        this.input = this.masker.apply(this.model).value;
        this.$watch('model', value => {
            this.input = this.masker.apply(value).value;
        });
    },
    onInput(value) {
        this.input = this.masker.apply(value).value;
        if (!this.config.isBlur) {
            this.emitInput();
        }
    },
    emitInput() {
        this.model = this.config.emitFormatted
            ? this.masker.value
            : this.masker.getOriginal();
    }
});
//# sourceMappingURL=maskable.js.map