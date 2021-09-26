<?php

namespace Lexik\Bundle\CurrencyBundle\Currency;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
interface ConverterInterface
{
    /**
     * Convert from default currency to another.
     */
    public function convert(float $value, string $targetCurrency, bool $round = true, string $valueCurrency = null): float;

    public function getDefaultCurrency(): string;
}
