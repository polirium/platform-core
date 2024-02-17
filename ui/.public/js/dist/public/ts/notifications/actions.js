"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.parseActions = exports.parseAction = void 0;
const parses_1 = require("./parses");
const parseAction = (options, componentId) => {
    if (options?.url)
        return (0, parses_1.parseRedirect)(options.url);
    if (options?.method && componentId)
        return (0, parses_1.parseLivewire)({ ...options, id: componentId });
    if (options?.dispatch)
        return (0, parses_1.parseLivewireDispatch)({ ...options });
    return () => null;
};
exports.parseAction = parseAction;
const getActionLabel = (options, action, actionName) => {
    const defaultLabels = { accept: 'Confirm', reject: 'Cancel' };
    return action?.label
        ?? options[`${actionName}Label`]
        ?? defaultLabels[actionName];
};
const parseActions = (options, componentId) => {
    if (options.method) {
        options.accept = Object.assign({
            method: options.method,
            params: options.params
        }, options.accept);
    }
    if (options.dispatch) {
        options.accept = Object.assign({
            dispatch: options.dispatch,
            to: options.to,
            params: options.params
        }, options.accept);
    }
    return Object.assign({}, ...['accept', 'reject'].map(actionName => {
        const action = Object.assign({}, options[actionName]);
        action.label = getActionLabel(options, action, actionName);
        if (!action.execute) {
            action.execute = (0, exports.parseAction)(action, componentId);
        }
        return { [actionName]: action };
    }));
};
exports.parseActions = parseActions;
//# sourceMappingURL=actions.js.map