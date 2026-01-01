<?php

namespace Polirium\Core\Base\Service\NumberToText\Providers;

use Polirium\Core\Base\Service\NumberToText\Contracts\NumberToTextProvider;

/**
 * French (Français) number to text provider
 */
class FrenchProvider implements NumberToTextProvider
{
    protected array $ones = [
        '', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
        'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept',
        'dix-huit', 'dix-neuf'
    ];

    protected array $tens = [
        '', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante',
        'soixante', 'quatre-vingt', 'quatre-vingt'
    ];

    protected array $currencies = [
        'EUR' => ['euro', 'euros'],
        'USD' => ['dollar', 'dollars'],
        'GBP' => ['livre', 'livres'],
        'VND' => ['dong', 'dongs'],
        'JPY' => ['yen', 'yens'],
        'CNY' => ['yuan', 'yuans'],
    ];

    public function convert(float|int $number, ?string $currency = null): string
    {
        $amount = round($number);

        if ($amount < 0) {
            return 'Moins ' . $this->convert(abs($amount), $currency);
        }

        if ($amount == 0) {
            return 'Zéro ' . $this->getCurrencyName(0, $currency);
        }

        $result = $this->convertToWords((int) $amount);
        $result = trim($result) . ' ' . $this->getCurrencyName($amount, $currency);

        return ucfirst(trim($result));
    }

    protected function convertToWords(int $number): string
    {
        if ($number < 20) {
            return $this->ones[$number];
        }

        if ($number < 100) {
            return $this->convertTens($number);
        }

        if ($number < 1000) {
            $hundreds = (int)($number / 100);
            $remainder = $number % 100;
            if ($hundreds == 1) {
                $result = 'cent';
            } else {
                $result = $this->ones[$hundreds] . ' cent';
                if ($remainder == 0) {
                    $result .= 's';
                }
            }
            if ($remainder > 0) {
                $result .= ' ' . $this->convertToWords($remainder);
            }
            return $result;
        }

        if ($number < 1000000) {
            $thousands = (int)($number / 1000);
            $remainder = $number % 1000;
            if ($thousands == 1) {
                $result = 'mille';
            } else {
                $result = $this->convertToWords($thousands) . ' mille';
            }
            if ($remainder > 0) {
                $result .= ' ' . $this->convertToWords($remainder);
            }
            return $result;
        }

        if ($number < 1000000000) {
            $millions = (int)($number / 1000000);
            $remainder = $number % 1000000;
            $result = $this->convertToWords($millions) . ' million';
            if ($millions > 1) {
                $result .= 's';
            }
            if ($remainder > 0) {
                $result .= ' ' . $this->convertToWords($remainder);
            }
            return $result;
        }

        $billions = (int)($number / 1000000000);
        $remainder = $number % 1000000000;
        $result = $this->convertToWords($billions) . ' milliard';
        if ($billions > 1) {
            $result .= 's';
        }
        if ($remainder > 0) {
            $result .= ' ' . $this->convertToWords($remainder);
        }
        return $result;
    }

    protected function convertTens(int $number): string
    {
        $ten = (int)($number / 10);
        $one = $number % 10;

        // Special cases for 70-79 and 90-99
        if ($ten == 7 || $ten == 9) {
            return $this->tens[$ten] . '-' . $this->ones[10 + $one];
        }

        // Special case for 80
        if ($ten == 8 && $one == 0) {
            return 'quatre-vingts';
        }

        $result = $this->tens[$ten];
        if ($one == 1 && $ten != 8) {
            $result .= ' et un';
        } elseif ($one > 0) {
            $result .= '-' . $this->ones[$one];
        }

        return $result;
    }

    public function getLocale(): string
    {
        return 'fr';
    }

    public function getDefaultCurrency(): string
    {
        return 'EUR';
    }

    protected function getCurrencyName(float|int $amount, ?string $currency): string
    {
        $currency = $currency ?? $this->getDefaultCurrency();

        if (!isset($this->currencies[$currency])) {
            return $currency;
        }

        $names = $this->currencies[$currency];
        return $amount == 1 ? $names[0] : $names[1];
    }
}
