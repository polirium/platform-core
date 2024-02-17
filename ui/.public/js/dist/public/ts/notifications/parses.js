"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.parseConfirmation = exports.parseNotification = exports.parseLivewireDispatch = exports.parseLivewire = exports.parseRedirect = void 0;
const actions_1 = require("./actions");
const events_1 = require("./events");
const icons_1 = require("./icons");
const parseRedirect = (redirect) => {
    return () => { window.location.href = redirect; };
};
exports.parseRedirect = parseRedirect;
const parseLivewire = ({ id, method, params = undefined }) => {
    return () => {
        const component = window.Livewire.find(id);
        if (params !== undefined) {
            return Array.isArray(params)
                ? component?.call(method, ...params)
                : component?.call(method, params);
        }
        component?.call(method);
    };
};
exports.parseLivewire = parseLivewire;
const parseLivewireDispatch = ({ dispatch, to = undefined, params = undefined }) => {
    return () => {
        const component = window.Livewire;
        if (to !== undefined) {
            if (params !== undefined) {
                return Array.isArray(params)
                    ? component?.dispatchTo(to, dispatch, ...params)
                    : component?.dispatchTo(to, dispatch, params);
            }
            component?.dispatchTo(to, dispatch);
        }
        else {
            if (params !== undefined) {
                return Array.isArray(params)
                    ? component?.dispatch(dispatch, ...params)
                    : component?.dispatch(dispatch, params);
            }
            component?.dispatch(dispatch);
        }
    };
};
exports.parseLivewireDispatch = parseLivewireDispatch;
const parseNotification = (options, componentId) => {
    const notification = Object.assign({
        closeButton: true,
        progressbar: true,
        timeout: 8500
    }, options);
    if (typeof options.icon === 'string') {
        notification.icon = (0, icons_1.parseIcon)({ name: options.icon, color: options.iconColor });
    }
    const { onClose, onDismiss, onTimeout } = (0, events_1.parseEvents)(options, componentId);
    return {
        ...notification,
        onClose,
        onDismiss,
        onTimeout
    };
};
exports.parseNotification = parseNotification;
const parseConfirmation = (options, componentId) => {
    const notification = (0, exports.parseNotification)(options, componentId);
    const { accept, reject } = (0, actions_1.parseActions)(options, componentId);
    return {
        ...notification,
        accept,
        reject
    };
};
exports.parseConfirmation = parseConfirmation;
//# sourceMappingURL=parses.js.map