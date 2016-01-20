<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * This class is used to create DoctrineCurrencyAdapter
 *
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class AdapterFactory
{
    /**
     * @var EntityManager
     */
    protected $doctrine;

    /**
     * @var array
     */
    private $currencies;

    /**
     * @var string
     */
    private $currencyClass;

    /**
     * __construct
     *
     * @param EntityManager $em
     */
    public function __construct(Registry $doctrine, $defaultCurrency, $availableCurrencies, $currencyClass)
    {
        $this->doctrine = $doctrine;

        $this->currencies = array();
        $this->currencies['default'] = $defaultCurrency;
        $this->currencies['managed'] = $availableCurrencies;
        $this->currencyClass = $currencyClass;
    }

    /**
     * Create an adaper from the given class.
     *
     * @param string $adapterClass
     * @return Lexik\Bundle\CurrencyBundle\Adapter\AbstractCurrencyAdapter
     */
    public function create($adapterClass)
    {
        $adapter = new $adapterClass();
        $adapter->setDefaultCurrency($this->currencies['default']);
        $adapter->setManagedCurrencies($this->currencies['managed']);
        $adapter->setCurrencyClass($this->currencyClass);

        return $adapter;
    }

    /**
     * Create a DoctrineCurrencyAdapter.
     *
     * @return Lexik\Bundle\CurrencyBundle\Adapter\DoctrineCurrencyAdapter
     */
    public function createDoctrineAdapter($adapterClass = null, $entityManagerName = null)
    {
        if (null == $adapterClass) {
            $adapterClass = 'Lexik\Bundle\CurrencyBundle\Adapter\DoctrineCurrencyAdapter';
        }
        $adapter = $this->create($adapterClass);

        $em = $this->doctrine->getManager($entityManagerName);
        $adapter->setManager($em);

        return $adapter;
    }

    /**
     * Create an EcbCurrencyAdapter.
     *
     * @return Lexik\Bundle\CurrencyBundle\Adapter\EcbCurrencyAdapter
     */
    public function createEcbAdapter($adapterClass = null)
    {
        if (null == $adapterClass) {
            $adapterClass = 'Lexik\Bundle\CurrencyBundle\Adapter\EcbCurrencyAdapter';
        }

        return $this->create($adapterClass);
    }

    /**
     * Create an OerCurrencyAdapter.
     *
     * @return Lexik\Bundle\CurrencyBundle\Adapter\OerCurrencyAdapter
     */
    public function createOerAdapter($adapterClass = null)
    {
        if (null == $adapterClass) {
            $adapterClass = 'Lexik\Bundle\CurrencyBundle\Adapter\OerCurrencyAdapter';
        }

        return $this->create($adapterClass);
    }

    /**
     * Create an YahooCurrencyAdapter.
     *
     * @return Lexik\Bundle\CurrencyBundle\Adapter\YahooCurrencyAdapter
     */
    public function createYahooAdapter($adapterClass = null)
    {
        if (null == $adapterClass) {
            $adapterClass = 'Lexik\Bundle\CurrencyBundle\Adapter\YahooCurrencyAdapter';
        }

        return $this->create($adapterClass);
    }
}