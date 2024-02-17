"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const notifications_1 = require("./notifications");
const dialog_1 = require("./dialog");
const dataGet_1 = require("./utils/dataGet");
require("./directives/confirm");
require("./browserSupport");
require("./alpine/store");
require("./alpine/magic");
require("./alpine/directives");
require("./components");
require("./global");
const wireui = {
    notify: notifications_1.notify,
    confirmNotification: notifications_1.confirmNotification,
    confirmAction: dialog_1.showConfirmDialog,
    dialog: dialog_1.showDialog,
    confirmDialog: dialog_1.showConfirmDialog,
    dataGet: dataGet_1.dataGet
};
window.$wireui = wireui;
document.addEventListener('DOMContentLoaded', () => window.Wireui.dispatchHook('load'));
exports.default = wireui;
//# sourceMappingURL=index.js.map