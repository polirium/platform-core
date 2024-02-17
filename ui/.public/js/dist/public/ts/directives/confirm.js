"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const dialog_1 = require("../dialog");
const getElements = (component) => {
    return [...component.querySelectorAll('[x-on\\:confirm]')]
        .filter(element => !element.getAttribute('x-on:click'));
};
const initialize = (component) => {
    const elements = getElements(component);
    elements.forEach(element => {
        const insideAlpineComponent = element.closest('[x-data]');
        const confirmData = element.getAttribute('x-on:confirm');
        const componentId = element.closest('[wire\\:id]')?.getAttribute('wire:id');
        if (insideAlpineComponent) {
            return element.setAttribute('x-on:click', `$wireui.confirmAction(${confirmData}, '${componentId}')`);
        }
        element.onclick = () => {
            const options = eval(`(${confirmData})`);
            (0, dialog_1.showConfirmDialog)(options, componentId);
        };
    });
};
document.addEventListener('livewire:init', () => {
    window.Livewire.hook('commit', ({ component, succeed }) => {
        succeed(() => {
            queueMicrotask(() => initialize(component.el));
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    initialize(document.body);
});
//# sourceMappingURL=confirm.js.map