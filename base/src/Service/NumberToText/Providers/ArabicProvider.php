<?php

namespace Polirium\Core\Base\Service\NumberToText\Providers;

use Polirium\Core\Base\Service\NumberToText\Contracts\NumberToTextProvider;

/**
 * Arabic (العربية) number to text provider
 */
class ArabicProvider implements NumberToTextProvider
{
    protected array $ones = [
        '', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة',
        'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر',
        'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'
    ];

    protected array $tens = [
        '', 'عشرة', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'
    ];

    protected array $currencies = [
        'SAR' => 'ريال سعودي',
        'AED' => 'درهم إماراتي',
        'EGP' => 'جنيه مصري',
        'USD' => 'دولار أمريكي',
        'EUR' => 'يورو',
        'VND' => 'دونج فيتنامي',
        'GBP' => 'جنيه إسترليني',
    ];

    public function convert(float|int $number, ?string $currency = null): string
    {
        $amount = round($number);

        if ($amount < 0) {
            return 'سالب ' . $this->convert(abs($amount), $currency);
        }

        if ($amount == 0) {
            return 'صفر ' . $this->getCurrencyName($currency);
        }

        $result = $this->convertToWords((int) $amount);

        return $result . ' ' . $this->getCurrencyName($currency);
    }

    protected function convertToWords(int $number): string
    {
        if ($number < 20) {
            return $this->ones[$number];
        }

        if ($number < 100) {
            $tens = (int)($number / 10);
            $ones = $number % 10;
            if ($ones == 0) {
                return $this->tens[$tens];
            }
            return $this->ones[$ones] . ' و' . $this->tens[$tens];
        }

        if ($number < 1000) {
            $hundreds = (int)($number / 100);
            $remainder = $number % 100;
            $result = $this->getHundreds($hundreds);
            if ($remainder > 0) {
                $result .= ' و' . $this->convertToWords($remainder);
            }
            return $result;
        }

        if ($number < 1000000) {
            $thousands = (int)($number / 1000);
            $remainder = $number % 1000;
            $result = $this->getThousands($thousands);
            if ($remainder > 0) {
                $result .= ' و' . $this->convertToWords($remainder);
            }
            return $result;
        }

        if ($number < 1000000000) {
            $millions = (int)($number / 1000000);
            $remainder = $number % 1000000;
            $result = $this->getMillions($millions);
            if ($remainder > 0) {
                $result .= ' و' . $this->convertToWords($remainder);
            }
            return $result;
        }

        $billions = (int)($number / 1000000000);
        $remainder = $number % 1000000000;
        $result = $this->getBillions($billions);
        if ($remainder > 0) {
            $result .= ' و' . $this->convertToWords($remainder);
        }
        return $result;
    }

    protected function getHundreds(int $num): string
    {
        $hundreds = [
            '', 'مائة', 'مئتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة',
            'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'
        ];
        return $hundreds[$num] ?? ($this->ones[$num] . ' مائة');
    }

    protected function getThousands(int $num): string
    {
        if ($num == 1) return 'ألف';
        if ($num == 2) return 'ألفان';
        if ($num <= 10) return $this->ones[$num] . ' آلاف';
        return $this->convertToWords($num) . ' ألف';
    }

    protected function getMillions(int $num): string
    {
        if ($num == 1) return 'مليون';
        if ($num == 2) return 'مليونان';
        if ($num <= 10) return $this->ones[$num] . ' ملايين';
        return $this->convertToWords($num) . ' مليون';
    }

    protected function getBillions(int $num): string
    {
        if ($num == 1) return 'مليار';
        if ($num == 2) return 'ملياران';
        if ($num <= 10) return $this->ones[$num] . ' مليارات';
        return $this->convertToWords($num) . ' مليار';
    }

    public function getLocale(): string
    {
        return 'ar';
    }

    public function getDefaultCurrency(): string
    {
        return 'SAR';
    }

    protected function getCurrencyName(?string $currency): string
    {
        $currency = $currency ?? $this->getDefaultCurrency();
        return $this->currencies[$currency] ?? $currency;
    }
}
