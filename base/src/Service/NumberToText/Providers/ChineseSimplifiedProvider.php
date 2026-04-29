<?php

namespace Polirium\Core\Base\Service\NumberToText\Providers;

use Polirium\Core\Base\Service\NumberToText\Contracts\NumberToTextProvider;

/**
 * Simplified Chinese (简体中文) number to text provider
 */
class ChineseSimplifiedProvider implements NumberToTextProvider
{
    protected array $digits = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
    protected array $units = ['', '十', '百', '千'];
    protected array $bigUnits = ['', '万', '亿', '兆'];

    protected array $currencies = [
        'CNY' => '元',
        'USD' => '美元',
        'EUR' => '欧元',
        'VND' => '越南盾',
        'JPY' => '日元',
        'GBP' => '英镑',
    ];

    public function convert(float|int $number, ?string $currency = null): string
    {
        $amount = round($number);

        if ($amount < 0) {
            return '负' . $this->convert(abs($amount), $currency);
        }

        if ($amount == 0) {
            return '零' . $this->getCurrencyName($currency);
        }

        $result = $this->convertToWords((int) $amount);
        $result = $this->cleanupResult($result);

        return $result . $this->getCurrencyName($currency);
    }

    protected function convertToWords(int $number): string
    {
        if ($number < 10) {
            return $this->digits[$number];
        }

        if ($number < 100) {
            $tens = (int)($number / 10);
            $ones = $number % 10;
            $result = ($tens == 1 ? '十' : $this->digits[$tens] . '十');
            if ($ones > 0) {
                $result .= $this->digits[$ones];
            }

            return $result;
        }

        if ($number < 1000) {
            $hundreds = (int)($number / 100);
            $remainder = $number % 100;
            $result = $this->digits[$hundreds] . '百';
            if ($remainder > 0) {
                if ($remainder < 10) {
                    $result .= '零';
                }
                $result .= $this->convertToWords($remainder);
            }

            return $result;
        }

        if ($number < 10000) {
            $thousands = (int)($number / 1000);
            $remainder = $number % 1000;
            $result = $this->digits[$thousands] . '千';
            if ($remainder > 0) {
                if ($remainder < 100) {
                    $result .= '零';
                }
                $result .= $this->convertToWords($remainder);
            }

            return $result;
        }

        // Handle 万 (10,000s)
        if ($number < 100000000) {
            $wan = (int)($number / 10000);
            $remainder = $number % 10000;
            $result = $this->convertToWords($wan) . '万';
            if ($remainder > 0) {
                if ($remainder < 1000) {
                    $result .= '零';
                }
                $result .= $this->convertToWords($remainder);
            }

            return $result;
        }

        // Handle 亿 (100,000,000s)
        $yi = (int)($number / 100000000);
        $remainder = $number % 100000000;
        $result = $this->convertToWords($yi) . '亿';
        if ($remainder > 0) {
            if ($remainder < 10000000) {
                $result .= '零';
            }
            $result .= $this->convertToWords($remainder);
        }

        return $result;
    }

    protected function cleanupResult(string $result): string
    {
        // Remove trailing zeros
        $result = preg_replace('/零+$/', '', $result);
        // Replace multiple zeros with single zero
        $result = preg_replace('/零+/', '零', $result);

        return $result;
    }

    public function getLocale(): string
    {
        return 'zh-Hans';
    }

    public function getDefaultCurrency(): string
    {
        return 'CNY';
    }

    protected function getCurrencyName(?string $currency): string
    {
        $currency = $currency ?? $this->getDefaultCurrency();

        return $this->currencies[$currency] ?? $currency;
    }
}
