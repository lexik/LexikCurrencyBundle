<?php

namespace Lexik\Bundle\CurrencyBundle\Currency;

use Lexik\Bundle\CurrencyBundle\Adapter\AbstractCurrencyAdapter;
use Lexik\Bundle\CurrencyBundle\Exception\CurrencyNotFoundException;

/**
 * Currency converter.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class Converter implements ConverterInterface
{
    /**
     * @var AbstractCurrencyAdapter
     */
    protected $adapter;

    /**
     * @var integer
     */
    protected $precision;

    /**
     * @var string
     */
    protected $roundMode;

    /**
     * Construct.
     *
     * @param AbstractCurrencyAdapter $adapter
     * @param integer                 $precision
     * @param string                  $roundMode
     * @throws \InvalidArgumentException
     */
    public function __construct(AbstractCurrencyAdapter $adapter, $precision = 2, $roundMode = 'up')
    {
        $allowedModes = array('up', 'down', 'even', 'odd');

        if (!in_array($roundMode, $allowedModes)) {
            throw new \InvalidArgumentException(sprintf('Invalid round mode "%s", please use one of the following values: %s', $roundMode, implode(', ', $allowedModes)));
        }

        $this->adapter = $adapter;
        $this->precision = $precision;
        $this->roundMode = constant(sprintf('PHP_ROUND_HALF_%s', strtoupper($roundMode)));
    }

    /**
     * {@inheritDoc}
     */
    public function convert($value, $targetCurrency, $round = true, $valueCurrency = null)
    {
        if (!isset($this->adapter[$targetCurrency])) {
            throw new CurrencyNotFoundException($targetCurrency);
        }

        if (null == $valueCurrency) {
            $valueCurrency = $this->getDefaultCurrency();
        }

        if (!isset($this->adapter[$valueCurrency])) {
            throw new CurrencyNotFoundException($valueCurrency);
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
     * {@inheritDoc}
     */
    public function getDefaultCurrency()
    {
        return $this->adapter->getDefaultCurrency();
    }
}
