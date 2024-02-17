"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const uuid_1 = require("./uuid");
const timeout_1 = require("./timeout");
const interval_1 = require("./interval");
const masker_1 = require("./masker");
const currency_1 = require("./currency");
const helpers_1 = require("./helpers");
const date_1 = require("./date");
const utilities = {
    uuid: uuid_1.uuid,
    timeout: timeout_1.timeout,
    interval: interval_1.interval,
    masker: masker_1.masker,
    mask: masker_1.applyMask,
    currency: currency_1.currency,
    occurrenceCount: helpers_1.occurrenceCount,
    date: date_1.date,
    getLocalTimezone: date_1.getLocalTimezone
};
exports.default = utilities;
//# sourceMappingURL=index.js.map