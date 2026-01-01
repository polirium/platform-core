<?php

namespace Polirium\Core\Base\Service\NumberToText\Providers;

use Polirium\Core\Base\Service\NumberToText\Contracts\NumberToTextProvider;

/**
 * Traditional Chinese (繁體中文) number to text provider
 */
class ChineseTraditionalProvider implements NumberToTextProvider
{
    protected array $digits = ['零', '壹', '貳', '參', '肆', '伍', '陸', '柒', '捌', '玖'];
    protected array $units = ['', '拾', '佰', '仟'];
    protected array $bigUnits = ['', '萬', '億', '兆'];

    protected array $currencies = [
        'TWD' => '元',
        'CNY' => '人民幣',
        'USD' => '美元',
        'EUR' => '歐元',
        'VND' => '越南盾',
        'JPY' => '日圓',
        'GBP' => '英鎊',
    ];

    public function convert(float|int $number, ?string $currency = null): string
    {
        $amount = round($number);

        if ($amount < 0) {
            return '負' . $this->convert(abs($amount), $currency);
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
            $result = $this->digits[$tens] . '拾';
            if ($ones > 0) {
                $result .= $this->digits[$ones];
            }
            return $result;
        }

        if ($number < 1000) {
            $hundreds = (int)($number / 100);
            $remainder = $number % 100;
            $result = $this->digits[$hundreds] . '佰';
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
            $result = $this->digits[$thousands] . '仟';
            if ($remainder > 0) {
                if ($remainder < 100) {
                    $result .= '零';
                }
                $result .= $this->convertToWords($remainder);
            }
            return $result;
        }

        // Handle 萬 (10,000s)
        if ($number < 100000000) {
            $wan = (int)($number / 10000);
            $remainder = $number % 10000;
            $result = $this->convertToWords($wan) . '萬';
            if ($remainder > 0) {
                if ($remainder < 1000) {
                    $result .= '零';
                }
                $result .= $this->convertToWords($remainder);
            }
            return $result;
        }

        // Handle 億 (100,000,000s)
        $yi = (int)($number / 100000000);
        $remainder = $number % 100000000;
        $result = $this->convertToWords($yi) . '億';
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
        $result = preg_replace('/零+$/', '', $result);
        $result = preg_replace('/零+/', '零', $result);
        return $result;
    }

    public function getLocale(): string
    {
        return 'zh-Hant';
    }

    public function getDefaultCurrency(): string
    {
        return 'TWD';
    }

    protected function getCurrencyName(?string $currency): string
    {
        $currency = $currency ?? $this->getDefaultCurrency();
        return $this->currencies[$currency] ?? $currency;
    }
}
