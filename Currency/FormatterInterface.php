<?php

namespace Lexik\Bundle\CurrencyBundle\Currency;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
interface FormatterInterface
{
    /**
     * Format a given value.
     *
     * @param string  $value
     * @param null    $valueCurrency
     * @param boolean $decimal
     * @param boolean $symbol
     * @return string
     */
    public function format($value, $valueCurrency = null, $decimal = true, $symbol = true);

    /**
     * Set the locale to use to format the value.
     *
     * @param string $locale
     */
    public function setLocale($locale);
}
