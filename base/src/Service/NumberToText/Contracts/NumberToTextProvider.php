<?php

namespace Polirium\Core\Base\Service\NumberToText\Contracts;

interface NumberToTextProvider
{
    /**
     * Convert number to text representation.
     *
     * @param float|int $number The number to convert
     * @param string|null $currency Currency code (VND, USD, EUR, etc.)
     * @return string Text representation of the number
     */
    public function convert(float|int $number, ?string $currency = null): string;

    /**
     * Get supported locale code.
     *
     * @return string Locale code (vi, en, zh, etc.)
     */
    public function getLocale(): string;

    /**
     * Get default currency for this locale.
     *
     * @return string Currency code
     */
    public function getDefaultCurrency(): string;
}
