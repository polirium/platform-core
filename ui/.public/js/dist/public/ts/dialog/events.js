"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.parseEvents = exports.events = exports.parseEvent = void 0;
const parses_1 = require("../notifications/parses");
const parseEvent = (options, componentId) => {
    if (options?.url)
        return (0, parses_1.parseRedirect)(options.url);
    if (options?.method && componentId)
        return (0, parses_1.parseLivewire)({ ...options, id: componentId });
    return () => null;
};
exports.parseEvent = parseEvent;
exports.events = ['onClose', 'onTimeout', 'onDismiss'];
const parseEvents = (options, componentId) => {
    return Object.assign({}, ...exports.events.map(eventName => {
        const event = options[eventName];
        if (typeof event === 'function') {
            return { [eventName]: event };
        }
        return { [eventName]: (0, exports.parseEvent)(event, componentId) };
    }));
};
exports.parseEvents = parseEvents;
//# sourceMappingURL=events.js.map