<?php

namespace Lexik\Bundle\CurrencyBundle\Exception;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class CurrencyNotFoundException extends \InvalidArgumentException
{
    public function __construct($currency)
    {
        parent::__construct(sprintf('Can\'t find currency: "%s"', $currency));
    }
}