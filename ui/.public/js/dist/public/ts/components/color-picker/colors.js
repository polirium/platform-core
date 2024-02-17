"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.makeColors = void 0;
const colors_1 = __importDefault(require("tailwindcss/colors"));
const excludeColors = [
    'lightBlue',
    'warmGray',
    'trueGray',
    'coolGray',
    'blueGray'
];
const makeColors = () => {
    excludeColors.forEach(color => delete colors_1.default[color]);
    const colors = Object.entries(colors_1.default).flatMap(([name, values]) => {
        if (typeof values === 'string' || excludeColors.includes(name)) {
            return [];
        }
        return Object.entries(values).map(([tonality, hex]) => ({
            name: `${name}-${tonality}`,
            value: hex
        }));
    });
    colors.push({ name: 'White', value: '#fff' });
    colors.push({ name: 'Black', value: '#000' });
    return colors;
};
exports.makeColors = makeColors;
//# sourceMappingURL=colors.js.map