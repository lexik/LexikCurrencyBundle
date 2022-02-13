<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use ArrayIterator;
use Lexik\Bundle\CurrencyBundle\Entity\Currency;

/**
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @extends ArrayIterator<string, Currency>
 */
abstract class AbstractCurrencyAdapter extends ArrayIterator
{
    protected string $defaultCurrency;

    /**
     * @var array<string>
     */
    protected array $managedCurrencies = [];

    /**
     * @var class-string
     */
    protected string $currencyClass;

    public function setDefaultCurrency(string $defaultCurrency): void
    {
        $this->defaultCurrency = $defaultCurrency;
    }

    public function getDefaultCurrency(): string
    {
        return $this->defaultCurrency;
    }

    /**
     * @param array<string> $currencies
     */
    public function setManagedCurrencies(array $currencies): void
    {
        $this->managedCurrencies = $currencies;
    }

    /**
     * @return array<string>
     */
    public function getManagedCurrencies(): array
    {
        return $this->managedCurrencies;
    }

    /**
     * @param class-string $currencyClass
     */
    public function setCurrencyClass(string $currencyClass): void
    {
        $this->currencyClass = $currencyClass;
    }

    /**
     * @param string $key
     * @param Currency $value
     */
    public function offsetSet($key, $value): void
    {
        if (!$value instanceof $this->currencyClass) {
            throw new \InvalidArgumentException(sprintf('$newval must be an instance of Currency, instance of "%s" given', $value::class));
        }

        parent::offsetSet($key, $value);
    }

    /**
     * @param Currency $value
     */
    public function append($value): void
    {
        if (!$value instanceof $this->currencyClass) {
            throw new \InvalidArgumentException(sprintf('$newval must be an instance of Currency, instance of "%s" given', $value::class));
        }

        parent::append($value);
    }

    protected function convertAll(float $rate): void
    {
        /** @var Currency $currency */
        foreach ($this as $currency) {
            $currency->convert($rate);
        }
    }

    /**
     * This method is used by the constructor
     * to attach all currencies.
     */
    abstract public function attachAll(): void;

    /**
     * Get identifier value for the adapter must be unique
     * for all the project
     */
    abstract public function getIdentifier(): string;
}
