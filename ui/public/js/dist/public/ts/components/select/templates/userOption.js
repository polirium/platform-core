"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.template = void 0;
const baseTemplate_1 = __importDefault(require("./baseTemplate"));
const template = (config) => ({
    config,
    render(option) {
        return (0, baseTemplate_1.default)(`
      <div class="flex items-center gap-x-3">
        <img src="${this.getSrc(option)}" class="shrink-0 h-6 w-6 object-cover rounded-full">

        <div :class="{ 'text-sm': Boolean(option.description) }">
          ${option.label}

          <span x-show="option.description" class="text-xs opacity-70">
            <br/> ${option.description}
          </span>
        </div>
      </div>
    `);
    },
    renderSelected(option) {
        return `
      <div class="flex items-center gap-x-3">
        <img src="${this.getSrc(option)}" class="shrink-0 h-6 w-6 object-cover rounded-full">

        <span>${option.label}</span>
      </div>
    `;
    },
    getSrc(option) {
        if (this.config.src) {
            return option[this.config.src];
        }
        return option.src;
    }
});
exports.template = template;
exports.default = exports.template;
//# sourceMappingURL=userOption.js.map