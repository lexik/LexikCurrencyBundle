<?php

namespace Lexik\Bundle\CurrencyBundle\Exception;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class CurrencyNotFoundException extends \InvalidArgumentException
{
    public function __construct(string $currency)
    {
        parent::__construct(sprintf('Cannot find currency: "%s"', $currency));
    }
}
