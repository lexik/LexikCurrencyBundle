<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\CurrencyBundle\Entity\Currency;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class DoctrineCurrencyAdapter extends AbstractCurrencyAdapter
{
    private EntityManager $manager;

    private bool $initialized = false;

    public function attachAll(): void
    {
        // nothing here
    }

    public function getIdentifier(): string
    {
        return 'doctrine';
    }

    public function offsetExists($index): bool
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return parent::offsetExists($index);
    }

    public function setManager(EntityManager $manager): void
    {
        $this->manager = $manager;
    }

    public function offsetGet(mixed $key): mixed
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return parent::offsetGet($key);
    }

    private function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * @throws Exception
     */
    private function initialize(): void
    {
        if (!isset($this->manager)) {
            throw new \RuntimeException('No ObjectManager set on DoctrineCurrencyAdapter.');
        }

        $currencies = $this->manager
            ->getRepository($this->currencyClass)
            ->findAll();

        /** @var Currency $currency */
        foreach ($currencies as $currency) {
            $this[$currency->getCode()] = $currency;
        }

        $this->initialized = true;
    }
}
