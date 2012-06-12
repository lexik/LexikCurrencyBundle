<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Doctrine\ORM\EntityManager;

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
    private $em;

    /**
     * @var array
     */
    private $currencies;

    /**
     * __construct
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, $defaultCurrency, $availableCurrencies)
    {
        $this->em = $em;

        $this->currencies = array();
        $this->currencies['default'] = $defaultCurrency;
        $this->currencies['managed'] = $availableCurrencies;
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

        return $adapter;
    }

    /**
     * Create a DoctrineCurrencyAdapter.
     *
     * @return Lexik\Bundle\CurrencyBundle\Adapter\DoctrineCurrencyAdapter
     */
    public function createDoctrineAdapter($adapterClass = null)
    {
        if (null == $adapterClass) {
            $adapterClass = 'Lexik\Bundle\CurrencyBundle\Adapter\DoctrineCurrencyAdapter';
        }

        $adapter = $this->create($adapterClass);

        $currencies = $this->em
            ->getRepository('Lexik\Bundle\CurrencyBundle\Entity\Currency')
            ->findAll();

        foreach ($currencies as $currency) {
            $adapter[$currency->getCode()] = $currency;
        }

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
}