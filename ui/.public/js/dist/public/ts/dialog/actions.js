"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.parseActions = exports.iconsMap = exports.parseAction = void 0;
const parses_1 = require("../notifications/parses");
const colors = ['primary', 'secondary', 'positive', 'negative', 'warning', 'info', 'dark'];
const parseAction = (options, componentId) => {
    if (options?.url)
        return (0, parses_1.parseRedirect)(options.url);
    if (options?.method && componentId)
        return (0, parses_1.parseLivewire)({ ...options, id: componentId });
    return () => null;
};
exports.parseAction = parseAction;
const getActionLabel = (options, action, actionName) => {
    const defaultLabels = { accept: 'Confirm', reject: 'Cancel' };
    return action?.label
        ?? options[`${actionName}Label`]
        ?? defaultLabels[actionName];
};
exports.iconsMap = {
    question: 'primary',
    success: 'positive',
    error: 'negative'
};
const parseActions = (options, componentId) => {
    if (options.method) {
        options.accept = Object.assign({
            method: options.method,
            params: options.params
        }, options.accept);
    }
    return Object.assign({}, ...['accept', 'reject', 'close'].map(actionName => {
        const action = Object.assign({}, options[actionName]);
        action.label = getActionLabel(options, action, actionName);
        if (!action.execute) {
            action.execute = (0, exports.parseAction)(action, componentId);
        }
        if (actionName === 'accept'
            && !action.color
            && typeof options.icon === 'string'
            && ['success', 'error', 'info', 'warning', 'question'].includes(options.icon)) {
            action.color = exports.iconsMap[options.icon] ?? options.icon;
        }
        if (actionName === 'accept' && !action.color) {
            action.color = 'primary';
        }
        return { [actionName]: action };
    }));
};
exports.parseActions = parseActions;
//# sourceMappingURL=actions.js.map