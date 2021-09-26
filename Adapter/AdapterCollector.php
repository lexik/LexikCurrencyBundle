<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use InvalidArgumentException;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
final class AdapterCollector
{
    /**
     * @var array<string, AbstractCurrencyAdapter>
     */
    private array $elements = [];

    public function add(AbstractCurrencyAdapter $adapter): void
    {
        $this->elements[$adapter->getIdentifier()] = $adapter;
    }

    public function get(string $key): AbstractCurrencyAdapter
    {
        if (!isset($this->elements[$key])) {
            throw new InvalidArgumentException('Adapter does not exist');
        }

        return $this->elements[$key];
    }
}
