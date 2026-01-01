<?php

namespace Polirium\Core\Base\Service\NumberToText\Providers;

use Polirium\Core\Base\Service\NumberToText\Contracts\NumberToTextProvider;

/**
 * Japanese (日本語) number to text provider
 */
class JapaneseProvider implements NumberToTextProvider
{
    protected array $digits = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
    protected array $tens = ['', '十', '二十', '三十', '四十', '五十', '六十', '七十', '八十', '九十'];

    protected array $currencies = [
        'JPY' => '円',
        'USD' => 'ドル',
        'EUR' => 'ユーロ',
        'VND' => 'ドン',
        'CNY' => '元',
        'GBP' => 'ポンド',
    ];

    public function convert(float|int $number, ?string $currency = null): string
    {
        $amount = round($number);

        if ($amount < 0) {
            return 'マイナス' . $this->convert(abs($amount), $currency);
        }

        if ($amount == 0) {
            return '零' . $this->getCurrencyName($currency);
        }

        $result = $this->convertToWords((int) $amount);

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
            $result = ($hundreds == 1 ? '百' : $this->digits[$hundreds] . '百');
            if ($remainder > 0) {
                $result .= $this->convertToWords($remainder);
            }
            return $result;
        }

        if ($number < 10000) {
            $thousands = (int)($number / 1000);
            $remainder = $number % 1000;
            $result = ($thousands == 1 ? '千' : $this->digits[$thousands] . '千');
            if ($remainder > 0) {
                $result .= $this->convertToWords($remainder);
            }
            return $result;
        }

        // Handle 万 (10,000s)
        if ($number < 100000000) {
            $man = (int)($number / 10000);
            $remainder = $number % 10000;
            $result = ($man == 1 ? '一万' : $this->convertToWords($man) . '万');
            if ($remainder > 0) {
                $result .= $this->convertToWords($remainder);
            }
            return $result;
        }

        // Handle 億 (100,000,000s)
        $oku = (int)($number / 100000000);
        $remainder = $number % 100000000;
        $result = ($oku == 1 ? '一億' : $this->convertToWords($oku) . '億');
        if ($remainder > 0) {
            $result .= $this->convertToWords($remainder);
        }
        return $result;
    }

    public function getLocale(): string
    {
        return 'ja';
    }

    public function getDefaultCurrency(): string
    {
        return 'JPY';
    }

    protected function getCurrencyName(?string $currency): string
    {
        $currency = $currency ?? $this->getDefaultCurrency();
        return $this->currencies[$currency] ?? $currency;
    }
}
