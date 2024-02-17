"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.baseComponent = void 0;
exports.baseComponent = {
    $cleanup(callback) {
        if (!this._x_cleanups)
            this._x_cleanups = [];
        this._x_cleanups.push(callback);
    }
};
//# sourceMappingURL=alpine.js.map