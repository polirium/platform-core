<?php

namespace Polirium\Core\Base\Service\NumberToText;

use Polirium\Core\Base\Service\NumberToText\Contracts\NumberToTextProvider;
use Polirium\Core\Base\Service\NumberToText\Providers\ArabicProvider;
use Polirium\Core\Base\Service\NumberToText\Providers\ChineseSimplifiedProvider;
use Polirium\Core\Base\Service\NumberToText\Providers\ChineseTraditionalProvider;
use Polirium\Core\Base\Service\NumberToText\Providers\EnglishProvider;
use Polirium\Core\Base\Service\NumberToText\Providers\FrenchProvider;
use Polirium\Core\Base\Service\NumberToText\Providers\JapaneseProvider;
use Polirium\Core\Base\Service\NumberToText\Providers\VietnameseProvider;

class NumberToTextService
{
    protected array $providers = [];

    public function __construct()
    {
        // Default providers
        $this->registerProvider(new VietnameseProvider());
        $this->registerProvider(new EnglishProvider());
        $this->registerProvider(new ChineseSimplifiedProvider());
        $this->registerProvider(new ChineseTraditionalProvider());
        $this->registerProvider(new JapaneseProvider());
        $this->registerProvider(new FrenchProvider());
        $this->registerProvider(new ArabicProvider());
    }

    /**
     * Register a custom provider.
     */
    public function registerProvider(NumberToTextProvider $provider): self
    {
        $this->providers[$provider->getLocale()] = $provider;
        return $this;
    }

    /**
     * Convert number to text.
     *
     * @param float|int $number The number to convert
     * @param string|null $locale Locale code (vi, en). Defaults to app locale
     * @param string|null $currency Currency code (VND, USD). Defaults to provider default
     * @return string Text representation
     */
    public function convert(float|int $number, ?string $locale = null, ?string $currency = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $provider = $this->getProvider($locale);

        return $provider->convert($number, $currency);
    }

    /**
     * Get provider for locale, falls back to Vietnamese.
     */
    protected function getProvider(string $locale): NumberToTextProvider
    {
        return $this->providers[$locale] ?? $this->providers['vi'];
    }

    /**
     * Get all registered locales.
     */
    public function getSupportedLocales(): array
    {
        return array_keys($this->providers);
    }

    /**
     * Check if locale is supported.
     */
    public function supportsLocale(string $locale): bool
    {
        return isset($this->providers[$locale]);
    }
}
