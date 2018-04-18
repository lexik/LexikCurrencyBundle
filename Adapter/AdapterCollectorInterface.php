<?php


namespace Lexik\Bundle\CurrencyBundle\Adapter;


interface AdapterCollectorInterface
{
    /**
     * Add an adapter
     *
     * @param mixed $key
     * @param AbstractCurrencyAdapter $adapter
     */
    public function add(AbstractCurrencyAdapter $adapter);

    /**
     * Get adapter
     *
     * @param mixed $key
     * @return AbstractCurrencyAdapter
     */
    public function get($key);
}
