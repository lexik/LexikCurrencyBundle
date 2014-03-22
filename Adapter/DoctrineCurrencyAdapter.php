<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class DoctrineCurrencyAdapter extends AbstractCurrencyAdapter
{
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
}
