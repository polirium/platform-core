"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.timer = void 0;
const timeout_1 = __importDefault(require("../utils/timeout"));
const interval_1 = __importDefault(require("../utils/interval"));
const makeInterval = (totalTimeout, delay, callback) => {
    let percentage = 100;
    let timeout = totalTimeout;
    const interval = (0, interval_1.default)(() => {
        timeout -= delay;
        percentage = Math.floor(timeout * 100 / totalTimeout);
        callback(percentage);
        if (timeout <= delay) {
            interval.pause();
        }
    }, delay);
    return interval;
};
const timer = (timeout, onTimeout, onInterval) => {
    const timer = (0, timeout_1.default)(onTimeout, timeout);
    const interval = makeInterval(timeout, 100, onInterval);
    return {
        pause: () => {
            timer.pause();
            interval.pause();
        },
        resume: () => {
            timer.resume();
            interval.resume();
        }
    };
};
exports.timer = timer;
//# sourceMappingURL=timer.js.map