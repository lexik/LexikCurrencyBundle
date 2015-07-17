<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

/**
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
abstract class AbstractCurrencyAdapter extends \ArrayIterator
{
    /**
     * @var string
     */
    protected $defaultCurrency;

    /**
     * @var array
     */
    protected $managedCurrencies = array();

    /**
     * @var string
     */
    protected $currencyClass;

    /**
     * Set default currency
     *
     * @param string $defaultCurrency
     */
    public function setDefaultCurrency($defaultCurrency)
    {
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * Get default currency
     *
     * @return string
     */
    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    /**
     * Get managedCurrencies
     *
     * @param array $currencies
     */
    public function setManagedCurrencies($currencies)
    {
        $this->managedCurrencies = $currencies;
    }

    /**
     * Get managedCurrencies
     *
     * @return array
     */
    public function getManagedCurrencies()
    {
        return $this->managedCurrencies;
    }

    /**
     * Get managedCurrencies
     *
     * @return array
     */
    public function setCurrencyClass($currencyClass)
    {
        return $this->currencyClass = $currencyClass;
    }

    /**
     * Set object
     *
     * @param mixed $index
     * @param Currency $newval
     */
    public function offsetSet($index, $newval)
    {
        if (!$newval instanceof $this->currencyClass) {
            throw new \InvalidArgumentException(sprintf('$newval must be an instance of Currency, instance of "%s" given', get_class($newval)));
        }

        parent::offsetSet($index, $newval);
    }

    /**
     * Append a value
     *
     * @param Currency $value
     */
    public function append($value)
    {
        if (!$value instanceof $this->currencyClass) {
            throw new \InvalidArgumentException(sprintf('$newval must be an instance of Currency, instance of "%s" given', get_class($value)));
        }

        parent::append($value);
    }

    /**
     * Convert all
     *
     * @param mixed $rate
     */
    protected function convertAll($rate)
    {
        foreach ($this as $currency) {
            $currency->convert($rate);
        }
    }

    /**
     * This method is used by the constructor
     * to attach all currencies.
     */
    abstract public function attachAll();

    /**
     * Get identier value for the adapter must be unique
     * for all the project
     *
     * @return string
     */
    abstract protected function getIdentifier();
}