<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Doctrine\ORM\EntityManager;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class DoctrineCurrencyAdapter extends AbstractCurrencyAdapter
{
    /**
     * @var EntityManager
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

    /**
     * {@inheritdoc}
     */
    public function offsetExists($index)
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return parent::offsetExists($index);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * @throws \Exception
     */
    private function initialize()
    {
        if (!isset($this->manager)) {
            throw new \RuntimeException('No ObjectManager set on DoctrineCurrencyAdapter.');
        }

        $currencies = $this->manager
            ->getRepository($this->currencyClass)
            ->findAll();

        foreach ($currencies as $currency) {
            $this[$currency->getCode()] = $currency;
        }

        $this->initialized = true;
    }
}
