"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.parseIcon = exports.icons = exports.colors = void 0;
exports.colors = {
    'success': 'text-positive-400',
    'error': 'text-negative-400',
    'info': 'text-info-400',
    'warning': 'text-warning-400',
    'question': 'text-secondary-400'
};
exports.icons = {
    'success': { name: 'check-circle', color: exports.colors['success'] },
    'error': { name: 'exclamation', color: exports.colors['error'] },
    'info': { name: 'information-circle', color: exports.colors['info'] },
    'warning': { name: 'exclamation-circle', color: exports.colors['warning'] },
    'question': { name: 'question-mark-circle', color: exports.colors['question'] }
};
const parseIcon = (options) => {
    if (exports.icons[options.name]) {
        const { name, color } = exports.icons[options.name];
        options.name = name;
        if (!options.color) {
            options.color = color;
        }
    }
    return options;
};
exports.parseIcon = parseIcon;
//# sourceMappingURL=icons.js.map