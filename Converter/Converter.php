<?php

namespace Lexik\Bundle\CurrencyBundle\Converter;

use Lexik\Bundle\CurrencyBundle\Adapter\AbstractCurrencyAdapter;
use Lexik\Bundle\CurrencyBundle\Exception\CurrencyNotFoundException;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class Converter
{
    /**
     * @var AbstractCurrencyAdapter
     */
    private $adapter;

    /**
     * @var int
     */
    private $precision;

    /**
     * @var string
     */
    private $roundMode;

    /**
     * Construct.
     *
     * @param AbstractCurrencyAdapter $adapter
     * @param int                     $precision
     * @param string                  $roundMode
     */
    public function __construct(AbstractCurrencyAdapter $adapter, $precision = 2, $roundMode = 'up')
    {
        $allowedModes = array('up', 'down', 'even', 'odd');

        if (!in_array($roundMode, $allowedModes)) {
            throw new \InvalidArgumentException(sprintf('Invalid round mode "%s", please use one off the follwing values: %s', $roundMode, implode(', ', $allowedModes)));
        }

        $this->adapter = $adapter;
        $this->precision = $precision;
        $this->roundMode = constant(sprintf('PHP_ROUND_HALF_%s', strtoupper($roundMode)));
    }

    /**
     * Convert from default currency to another
     *
     * @param float   $value
     * @param string  $targetCurrency
     * @param boolean $round
     * @param string  $valueCurrency
     * @return float
     */
    public function convert($value, $targetCurrency, $round = true, $valueCurrency = null)
    {
        if (!isset($this->adapter[$targetCurrency])) {
            throw new CurrencyNotFoundException($targetCurrency);
        }

        if (null == $valueCurrency) {
            $valueCurrency = $this->getDefaultCurrency();
        }

        if ($targetCurrency != $valueCurrency) {
            if ($this->getDefaultCurrency() == $valueCurrency) {
                $value *= $this->adapter[$targetCurrency]->getRate();

            } else {
                $value /= $this->adapter[$valueCurrency]->getRate(); // value in the default currency

                if ($this->getDefaultCurrency() != $targetCurrency) {
                    $value *= $this->adapter[$targetCurrency]->getRate();
                }
            }
        }

        return $round ? round($value, $this->precision, $this->roundMode) : $value;
    }

    /**
     * Get default currency
     *
     * @return string
     */
    public function getDefaultCurrency()
    {
        return $this->adapter->getDefaultCurrency();
    }
}
