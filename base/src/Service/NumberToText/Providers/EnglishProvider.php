<?php

namespace Polirium\Core\Base\Service\NumberToText\Providers;

use Polirium\Core\Base\Service\NumberToText\Contracts\NumberToTextProvider;

class EnglishProvider implements NumberToTextProvider
{
    protected array $ones = [
        '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
        'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
        'seventeen', 'eighteen', 'nineteen',
    ];

    protected array $tens = [
        '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety',
    ];

    protected array $scales = [
        '', 'thousand', 'million', 'billion', 'trillion', 'quadrillion',
    ];

    protected array $currencies = [
        'USD' => ['dollar', 'dollars'],
        'EUR' => ['euro', 'euros'],
        'GBP' => ['pound', 'pounds'],
        'VND' => ['dong', 'dong'],
        'JPY' => ['yen', 'yen'],
        'CNY' => ['yuan', 'yuan'],
    ];

    public function convert(float|int $number, ?string $currency = null): string
    {
        $amount = round($number);

        if ($amount < 0) {
            return 'Negative ' . $this->convert(abs($amount), $currency);
        }

        if ($amount == 0) {
            return 'Zero ' . $this->getCurrencyName(0, $currency);
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
            return $this->tens[(int)($number / 10)] .
                ($number % 10 ? '-' . $this->ones[$number % 10] : '');
        }

        if ($number < 1000) {
            return $this->ones[(int)($number / 100)] . ' hundred' .
                ($number % 100 ? ' and ' . $this->convertToWords($number % 100) : '');
        }

        // Handle thousands, millions, billions, etc.
        $result = '';
        $scaleIndex = 0;

        while ($number > 0) {
            $chunk = $number % 1000;

            if ($chunk > 0) {
                $chunkText = $this->convertToWords($chunk);
                if ($scaleIndex > 0) {
                    $chunkText .= ' ' . $this->scales[$scaleIndex];
                }
                $result = $chunkText . ($result ? ' ' . $result : '');
            }

            $number = (int)($number / 1000);
            $scaleIndex++;
        }

        return $result;
    }

    public function getLocale(): string
    {
        return 'en';
    }

    public function getDefaultCurrency(): string
    {
        return 'USD';
    }

    protected function getCurrencyName(float|int $amount, ?string $currency): string
    {
        $currency = $currency ?? $this->getDefaultCurrency();

        if (! isset($this->currencies[$currency])) {
            return $currency;
        }

        $names = $this->currencies[$currency];

        return $amount == 1 ? $names[0] : $names[1];
    }
}
