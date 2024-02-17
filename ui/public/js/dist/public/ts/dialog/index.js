"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.dialogs = exports.showConfirmDialog = exports.showDialog = void 0;
const parses_1 = require("./parses");
const makeEventName = (id) => {
    const event = 'dialog';
    if (id) {
        return `${event}:${id}`;
    }
    return event;
};
const showDialog = (options, componentId) => {
    const event = new CustomEvent(`wireui:${makeEventName(options.id)}`, { detail: { options, componentId } });
    window.dispatchEvent(event);
};
exports.showDialog = showDialog;
const showConfirmDialog = (options, componentId) => {
    if (!options.icon) {
        options.icon = 'question';
    }
    const event = new CustomEvent(`wireui:confirm-${makeEventName(options.id)}`, { detail: { options, componentId } });
    window.dispatchEvent(event);
};
exports.showConfirmDialog = showConfirmDialog;
exports.dialogs = {
    parseDialog: parses_1.parseDialog,
    parseConfirmation: parses_1.parseConfirmation
};
//# sourceMappingURL=index.js.map