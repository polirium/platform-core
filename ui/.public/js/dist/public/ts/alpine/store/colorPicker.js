"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const colors_1 = require("@/components/color-picker/colors");
const store = {
    colors: (0, colors_1.makeColors)(),
    setColors(colors) {
        this.colors = colors;
        return this;
    }
};
exports.default = store;
//# sourceMappingURL=colorPicker.js.map