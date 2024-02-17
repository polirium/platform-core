"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.interval = void 0;
const interval = (callback, delay) => {
    let timerId = delay;
    let remaining = delay;
    let start = new Date();
    const resume = () => {
        start = new Date();
        timerId = window.setTimeout(() => {
            remaining = delay;
            resume();
            callback();
        }, remaining);
    };
    const pause = () => {
        window.clearTimeout(timerId);
        remaining -= new Date().getTime() - start.getTime();
    };
    resume();
    return { pause, resume };
};
exports.interval = interval;
exports.default = exports.interval;
//# sourceMappingURL=interval.js.map