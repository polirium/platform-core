"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.dataGet = void 0;
const dataGet = (target, path, fallback = undefined) => {
    if (!path || [null, undefined].includes(target) || ['boolean', 'number', 'string'].includes(typeof target)) {
        return target;
    }
    const segments = Array.isArray(path) ? path : path.split('.');
    const segment = segments[0];
    let find = target;
    if (segment !== '*' && segments.length > 0) {
        if (find[segment] === null || typeof find[segment] === 'undefined') {
            find = typeof fallback === 'function' ? fallback() : fallback;
        }
        else {
            find = (0, exports.dataGet)(find[segment], segments.slice(1), fallback);
        }
    }
    else if (segment === '*') {
        const partial = segments.slice(path.indexOf('*') + 1, path.length);
        if (typeof find === 'object') {
            find = Object.keys(find).reduce((build, property) => ({
                ...build,
                [property]: (0, exports.dataGet)(find[property], partial, fallback)
            }), {});
        }
        else {
            find = (0, exports.dataGet)(find, partial, fallback);
        }
    }
    if (typeof find === 'object' && Object.keys(find).length > 0) {
        const isArrayTransformable = Object.keys(find).every(index => index.match(/^(0|[1-9][0-9]*)$/));
        return isArrayTransformable ? Object.values(find) : find;
    }
    return find;
};
exports.dataGet = dataGet;
exports.default = exports.dataGet;
//# sourceMappingURL=dataGet.js.map