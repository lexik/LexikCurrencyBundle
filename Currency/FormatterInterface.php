<?php

namespace Lexik\Bundle\CurrencyBundle\Currency;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
interface FormatterInterface
{
    public function format(float $value, string $valueCurrency = '', bool $decimal = true, bool $symbol = true): ?string;

    public function setLocale(string $locale): void;
}
