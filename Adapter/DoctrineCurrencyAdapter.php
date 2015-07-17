<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Doctrine\ORM\EntityManager;
use Exception;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class DoctrineCurrencyAdapter extends AbstractCurrencyAdapter
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * {@inheritdoc}
     */
    public function attachAll()
    {
        // nothing here
    }

    /**
     * Return identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'doctrine';
    }

    /**
     * @param EntityManager $manager
     */
    public function setManager(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function offsetExists($index)
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }
        return parent::offsetExists($index);
    }

    public function offsetGet($index)
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return parent::offsetGet($index);
    }

    /**
     * @return bool
     */
    private function isInitialized()
    {
        return $this->initialized;
    }

    private function initialize()
    {
        if (!isset($this->manager)) {
            throw new Exception('No ObjectManager set');
        }

        $currencies = $this->manager
            ->getRepository($this->currencyClass)
            ->findAll();

        foreach ($currencies as $currency) {
            $this[$currency->getCode()] = $currency;
        }
    }
}
