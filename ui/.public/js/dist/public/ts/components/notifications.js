"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const parses_1 = require("../notifications/parses");
const timer_1 = require("../notifications/timer");
const uuid_1 = __importDefault(require("../utils/uuid"));
exports.default = () => ({
    notifications: [],
    init() {
        this.$nextTick(() => {
            window.Wireui.dispatchHook('notifications:load');
        });
    },
    proccessNotification(notification) {
        notification.id = (0, uuid_1.default)();
        if (notification.timeout) {
            notification.timer = (0, timer_1.timer)(notification.timeout, () => {
                notification.onClose();
                notification.onTimeout();
                this.removeNotification(notification.id);
            }, (percentage) => {
                const progressBar = document.getElementById(`timeout.bar.${notification.id}`);
                if (!progressBar)
                    return;
                progressBar.style.width = `${percentage}%`;
            });
        }
        this.notifications.push(notification);
        if (notification.icon) {
            this.$nextTick(() => {
                this.fillNotificationIcon(notification);
            });
        }
    },
    addNotification(data) {
        const { options, componentId } = Array.isArray(data) ? data[0] : data;
        const notification = (0, parses_1.parseNotification)(options, componentId);
        this.proccessNotification(notification);
    },
    addConfirmNotification(data) {
        const { options, componentId } = Array.isArray(data) ? data[0] : data;
        const notification = (0, parses_1.parseConfirmation)(options, componentId);
        this.proccessNotification(notification);
    },
    fillNotificationIcon(notification) {
        const classes = `w-6 h-6 ${notification.icon.color}`.split(' ');
        fetch(`/wireui/icons/outline/${notification.icon.name}`)
            .then(response => response.text())
            .then(text => {
            const svg = new DOMParser().parseFromString(text, 'image/svg+xml').documentElement;
            svg.classList.add(...classes);
            document
                ?.getElementById(`notification.${notification.id}`)
                ?.querySelector('.notification-icon')
                ?.replaceChildren(svg);
        });
    },
    removeNotification(id) {
        const index = this.notifications.findIndex(notification => notification.id === id);
        if (~index) {
            this.notifications.splice(index, 1);
        }
    },
    closeNotification(notification) {
        notification.onClose();
        notification.onDismiss();
        this.removeNotification(notification.id);
    },
    pauseNotification(notification) {
        notification.timer?.pause();
    },
    resumeNotification(notification) {
        notification.timer?.resume();
    },
    accept(notification) {
        notification.onClose();
        notification.accept.execute();
        this.removeNotification(notification.id);
    },
    reject(notification) {
        notification.onClose();
        notification.reject.execute();
        this.removeNotification(notification.id);
    }
});
//# sourceMappingURL=notifications.js.map