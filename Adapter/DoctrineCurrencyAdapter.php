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
     * {@inheritdoc}
     */
    public function attachAll()
    {

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
