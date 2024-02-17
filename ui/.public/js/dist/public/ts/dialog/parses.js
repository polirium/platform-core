"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.parseConfirmation = exports.parseDialog = void 0;
const actions_1 = require("./actions");
const events_1 = require("./events");
const icons_1 = require("./icons");
const parseDialog = (options, componentId) => {
    const dialog = Object.assign({
        closeButton: true,
        progressbar: true,
        style: 'center',
        close: 'OK'
    }, options);
    if (typeof dialog.icon === 'string') {
        dialog.icon = (0, icons_1.parseIcon)({
            name: dialog.icon,
            color: options.iconColor,
            background: options.iconBackground
        });
    }
    if (typeof dialog.close === 'string') {
        dialog.close = { label: dialog.close };
    }
    if (typeof dialog.close === 'object'
        && !dialog.close.color
        && typeof options.icon === 'string'
        && ['success', 'error', 'info', 'warning', 'question'].includes(options.icon)) {
        dialog.close.color = actions_1.iconsMap[options.icon] ?? options.icon;
    }
    const { onClose, onDismiss, onTimeout } = (0, events_1.parseEvents)(options, componentId);
    return {
        ...dialog,
        onClose,
        onDismiss,
        onTimeout
    };
};
exports.parseDialog = parseDialog;
const parseConfirmation = (options, componentId) => {
    options = Object.assign({ style: 'inline' }, options);
    const dialog = (0, exports.parseDialog)(options, componentId);
    const { accept, reject } = (0, actions_1.parseActions)(options, componentId);
    return {
        ...dialog,
        accept,
        reject
    };
};
exports.parseConfirmation = parseConfirmation;
//# sourceMappingURL=parses.js.map