"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.watchProps = exports.props = void 0;
const props = function (el) {
    const $root = window.Alpine.evaluate(el, '$root');
    const attribute = $root?.getAttribute('x-props');
    if (!attribute)
        return {};
    return window.Alpine.evaluate($root, attribute);
};
exports.props = props;
function watchProps(component, callback) {
    const observer = new MutationObserver((mutations) => callback(mutations));
    observer.observe(component.$root, { attributes: true });
    component.$cleanup(() => observer.disconnect());
}
exports.watchProps = watchProps;
exports.default = exports.props;
//# sourceMappingURL=props.js.map