"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.template = void 0;
const baseTemplate_1 = __importDefault(require("./baseTemplate"));
const template = () => ({
    render(option) {
        return (0, baseTemplate_1.default)(`
      <div>
        ${option.label}

        <span x-show="option.description" class="text-xs opacity-70">
            <br/> ${option.description}
          </span>
      </div>
    `);
    },
    renderSelected(option) {
        return `<span>${option.label}</span>`;
    }
});
exports.template = template;
exports.default = exports.template;
//# sourceMappingURL=option.js.map