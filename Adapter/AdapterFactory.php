<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class AdapterFactory
{
    /**
     * @var array{default: string, managed: array<string>}
     */
    private array $currencies;

    /**
     * @param Registry $doctrine
     * @param string $defaultCurrency
     * @param array<string> $availableCurrencies
     * @param class-string $currencyClass
     */
    public function __construct(
        protected Registry $doctrine,
        string $defaultCurrency,
        array $availableCurrencies,
        private string $currencyClass
    ) {
        $this->currencies = [
            'default' => $defaultCurrency,
            'managed' => $availableCurrencies
        ];
    }

    /**
     * @param class-string $adapterClass
     */
    public function create(string $adapterClass): AbstractCurrencyAdapter
    {
        /** @var AbstractCurrencyAdapter $adapter */
        $adapter = new $adapterClass();
        $adapter->setDefaultCurrency($this->currencies['default']);
        $adapter->setManagedCurrencies($this->currencies['managed']);
        $adapter->setCurrencyClass($this->currencyClass);

        return $adapter;
    }

    /**
     * @param ?class-string $adapterClass
     */
    public function createDoctrineAdapter(string $adapterClass = null, string $entityManagerName = null): AbstractCurrencyAdapter
    {
        if (null == $adapterClass) {
            $adapterClass = DoctrineCurrencyAdapter::class;
        }
        /** @var DoctrineCurrencyAdapter $adapter */
        $adapter = $this->create($adapterClass);

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager($entityManagerName);
        $adapter->setManager($em);

        return $adapter;
    }

    /**
     * @param ?class-string $adapterClass
     */
    public function createEcbAdapter(string $adapterClass = null): AbstractCurrencyAdapter
    {
        if (null == $adapterClass) {
            $adapterClass = EcbCurrencyAdapter::class;
        }

        return $this->create($adapterClass);
    }

    /**
     * @param ?class-string $adapterClass
     */
    public function createOerAdapter(string $adapterClass = null): AbstractCurrencyAdapter
    {
        if (null == $adapterClass) {
            $adapterClass = OerCurrencyAdapter::class;
        }

        return $this->create($adapterClass);
    }

    /**
     * @param ?class-string $adapterClass
     */
    public function createYahooAdapter(string $adapterClass = null): AbstractCurrencyAdapter
    {
        if (null == $adapterClass) {
            $adapterClass = YahooCurrencyAdapter::class;
        }

        return $this->create($adapterClass);
    }
}
