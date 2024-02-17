"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const debounce_1 = __importDefault(require("@/utils/debounce"));
const masker_1 = require("@/utils/masker");
const alpine_1 = require("@/components/alpine");
const positioning_1 = require("@/components/modules/positioning");
exports.default = (options = { colorNameAsValue: false }) => ({
    ...alpine_1.baseComponent,
    ...positioning_1.positioning,
    $refs: {},
    selected: { value: null, name: null },
    masker: (0, masker_1.masker)('!#XXXXXX', null),
    wireModel: options.wireModel ?? null,
    get colors() {
        if (options.colors)
            return options.colors;
        return [];
    },
    init() {
        this.initPositioningSystem();
        if (this.$refs.input.value) {
            this.setColor(this.$refs.input.value);
        }
        if (options.wireModel !== undefined) {
            this.initWireModel();
        }
    },
    initWireModel() {
        this.setColor(this.wireModel);
        const emitInput = this.emitInput.bind(this);
        if (options.wireModifiers?.blur) {
            this.$refs.input.addEventListener('blur', emitInput);
            this.$cleanup(() => this.$refs.input.removeEventListener('blur', emitInput));
        }
        else if (options.wireModifiers?.debounce?.exists) {
            this.$watch('selected', (0, debounce_1.default)(emitInput, options.wireModifiers.debounce.delay));
        }
        else {
            this.$watch('selected', (0, debounce_1.default)(emitInput, 300));
        }
        this.$watch('wireModel', (color) => this.setColor(color));
    },
    select(color) {
        this.selected = color;
        this.emitInput();
        this.close();
    },
    setColor(value) {
        if (!options.colorNameAsValue) {
            value = (0, masker_1.applyMask)('!#XXXXXX', value);
        }
        const color = this.colors.find(c => {
            if (options.colorNameAsValue)
                return c.name === value;
            return (0, masker_1.applyMask)('!#XXXXXX', c.value) === value;
        });
        this.selected = {
            value: color?.value ?? value,
            name: color?.name ?? value
        };
    },
    emitInput() {
        if (options.colorNameAsValue) {
            return (this.wireModel = this.selected.name);
        }
        this.wireModel = this.selected.value;
    }
});
//# sourceMappingURL=index.js.map