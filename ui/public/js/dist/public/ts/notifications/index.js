"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.notifications = exports.confirmNotification = exports.notify = void 0;
const icons_1 = require("./icons");
const parses_1 = require("./parses");
const timer_1 = require("./timer");
const notify = (options, componentId) => {
    const event = new CustomEvent('wireui:notification', { detail: { options, componentId } });
    window.dispatchEvent(event);
};
exports.notify = notify;
const confirmNotification = (options, componentId) => {
    options = Object.assign({
        icon: icons_1.icons['warning'],
        title: 'Are you sure?',
        description: 'You won\'t be able to revert this!'
    }, options);
    const event = new CustomEvent('wireui:confirm-notification', { detail: { options, componentId } });
    window.dispatchEvent(event);
};
exports.confirmNotification = confirmNotification;
exports.notifications = {
    parseNotification: parses_1.parseNotification,
    parseConfirmation: parses_1.parseConfirmation,
    timer: timer_1.timer
};
//# sourceMappingURL=index.js.map