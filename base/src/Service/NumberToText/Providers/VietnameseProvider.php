<?php

namespace Polirium\Core\Base\Service\NumberToText\Providers;

use Polirium\Core\Base\Service\NumberToText\Contracts\NumberToTextProvider;

class VietnameseProvider implements NumberToTextProvider
{
    protected array $digits = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
    protected array $units = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ', 'tỷ tỷ'];

    protected array $currencies = [
        'VND' => 'đồng',
        'USD' => 'đô la Mỹ',
        'EUR' => 'euro',
        'GBP' => 'bảng Anh',
        'JPY' => 'yên Nhật',
        'CNY' => 'nhân dân tệ',
    ];

    public function convert(float|int $number, ?string $currency = null): string
    {
        $amount = round($number);

        if ($amount < 0) {
            return 'Âm ' . $this->convert(abs($amount), $currency);
        }

        if ($amount == 0) {
            return 'Không ' . $this->getCurrencyName($currency);
        }

        $length = strlen((string) $amount);
        $unread = array_fill(0, $length, 0);

        // Mark trailing zeros in groups of 3
        for ($i = 0; $i < $length; $i++) {
            $digit = substr((string) $amount, $length - $i - 1, 1);

            if (($digit == 0) && ($i % 3 == 0) && ($unread[$i] == 0)) {
                for ($j = $i + 1; $j < $length; $j++) {
                    $nextDigit = substr((string) $amount, $length - $j - 1, 1);
                    if ($nextDigit != 0) {
                        break;
                    }
                }

                if (intval(($j - $i) / 3) > 0) {
                    for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++) {
                        $unread[$k] = 1;
                    }
                }
            }
        }

        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $digit = (int) substr((string) $amount, $length - $i - 1, 1);

            if ($unread[$i] == 1) {
                continue;
            }

            if (($i % 3 == 0) && ($i > 0)) {
                $result = $this->units[$i / 3] . ' ' . $result;
            }

            if ($i % 3 == 2) {
                $result = 'trăm ' . $result;
            }

            if ($i % 3 == 1) {
                $result = 'mươi ' . $result;
            }

            $result = $this->digits[$digit] . ' ' . $result;
        }

        // Vietnamese special replacements
        $result = str_replace('không mươi', 'lẻ', $result);
        $result = str_replace('lẻ không', '', $result);
        $result = str_replace('mươi không', 'mươi', $result);
        $result = str_replace('một mươi', 'mười', $result);
        $result = str_replace('mươi năm', 'mươi lăm', $result);
        $result = str_replace('mươi một', 'mươi mốt', $result);
        $result = str_replace('mười năm', 'mười lăm', $result);

        // Normalize multiple spaces to single
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result) . ' ' . $this->getCurrencyName($currency);

        return ucfirst(trim($result));
    }

    public function getLocale(): string
    {
        return 'vi';
    }

    public function getDefaultCurrency(): string
    {
        return 'VND';
    }

    protected function getCurrencyName(?string $currency): string
    {
        $currency = $currency ?? $this->getDefaultCurrency();

        return $this->currencies[$currency] ?? $currency;
    }
}
