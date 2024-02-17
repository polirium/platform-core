"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.parseIcon = exports.icons = void 0;
const icons_1 = require("../notifications/icons");
exports.icons = {
    'success': {
        name: 'check',
        color: icons_1.colors['success'],
        background: 'p-2 bg-positive-50 dark:bg-secondary-700 border dark:border-0 rounded-3xl'
    },
    'error': {
        name: 'exclamation',
        color: icons_1.colors['error'],
        background: 'p-2 bg-negative-50 dark:bg-secondary-700 border dark:border-0 rounded-3xl'
    },
    'info': {
        name: 'information-circle',
        color: icons_1.colors['info'],
        background: 'p-2 bg-info-50 dark:bg-secondary-700 border dark:border-0 rounded-3xl'
    },
    'warning': {
        name: 'exclamation-circle',
        color: icons_1.colors['warning'],
        background: 'p-2 bg-warning-50 dark:bg-secondary-700 border dark:border-0 rounded-3xl'
    },
    'question': {
        name: 'question-mark-circle',
        color: icons_1.colors['question'],
        background: 'p-2 bg-secondary-50 dark:bg-secondary-700 border dark:border-0 rounded-3xl'
    }
};
const parseIcon = (options) => {
    if (exports.icons[options.name]) {
        const { name, color, background } = exports.icons[options.name];
        options.name = name;
        if (!options.style) {
            options.style = 'outline';
        }
        if (!options.color) {
            options.color = color;
        }
        if (!options.background) {
            options.background = background;
        }
    }
    return options;
};
exports.parseIcon = parseIcon;
//# sourceMappingURL=icons.js.map