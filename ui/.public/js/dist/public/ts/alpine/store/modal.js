"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const store = {
    current: null,
    actives: [],
    setCurrent(id) {
        this.current = id;
        this.actives.push(id);
        return this;
    },
    remove(id) {
        if (this.current === id) {
            this.current = null;
        }
        this.actives = this.actives.filter(active => active !== id);
        if (this.current === null && this.actives.length) {
            this.current = this.actives[this.actives.length - 1];
        }
        return this;
    },
    isCurrent(id) {
        return this.current === id;
    },
    isFirstest(id) {
        return this.actives[0] === id;
    }
};
exports.default = store;
//# sourceMappingURL=modal.js.map