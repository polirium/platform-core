"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const dropdown_1 = __importDefault(require("./dropdown"));
const modal_1 = __importDefault(require("./modal"));
const dialog_1 = __importDefault(require("./dialog"));
const notifications_1 = __importDefault(require("./notifications"));
const maskable_1 = __importDefault(require("./inputs/maskable"));
const currency_1 = __importDefault(require("./inputs/currency"));
const number_1 = __importDefault(require("./inputs/number"));
const password_1 = __importDefault(require("./inputs/password"));
const select_1 = __importDefault(require("./select"));
const time_picker_1 = __importDefault(require("./time-picker"));
const datetime_picker_1 = __importDefault(require("./datetime-picker"));
document.addEventListener('alpine:init', () => {
    window.Alpine.data('wireui_dropdown', dropdown_1.default);
    window.Alpine.data('wireui_modal', modal_1.default);
    window.Alpine.data('wireui_dialog', dialog_1.default);
    window.Alpine.data('wireui_notifications', notifications_1.default);
    window.Alpine.data('wireui_inputs_maskable', maskable_1.default);
    window.Alpine.data('wireui_inputs_currency', currency_1.default);
    window.Alpine.data('wireui_inputs_number', number_1.default);
    window.Alpine.data('wireui_inputs_password', password_1.default);
    window.Alpine.data('wireui_select', select_1.default);
    window.Alpine.data('wireui_timepicker', time_picker_1.default);
    window.Alpine.data('wireui_datetime_picker', datetime_picker_1.default);
});
//# sourceMappingURL=index.js.map