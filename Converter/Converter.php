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
     * Construct.
     *
     * @param AbstractCurrencyAdapter $adapter
     */
    public function __construct(AbstractCurrencyAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Convert from default currency to another
     *
     * @param mixed $value
     * @param string $targetCurrency
     * @param boolean $round
     * @param string $valueCurrency
     * @return mixed
     */
    public function convert($value, $targetCurrency, $round = true, $valueCurrency = null)
    {
        if (null == $valueCurrency) {
            $valueCurrency = $this->getDefaultCurrency();
        }

        if (!isset($this->adapter[$targetCurrency])) {
            throw new CurrencyNotFoundException($targetCurrency);
        }

        if ($targetCurrency != $valueCurrency) {
            if ($this->getDefaultCurrency() == $valueCurrency) {
                $value *= $this->adapter[$targetCurrency]->getRate();
            }
            else {
                $value /= $this->adapter[$valueCurrency]->getRate(); // value in the default currency

                if ($this->getDefaultCurrency() != $targetCurrency) {
                    $value *= $this->adapter[$targetCurrency]->getRate();
                }
            }
        }

        return $round ? round($value, 2) : $value;
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