<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Fixtures;

use Lexik\Bundle\CurrencyBundle\Entity\Currency;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Tests fixtures class.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class CurrencyData implements FixtureInterface
{
    /**
     * @see Doctrine\Common\DataFixtures.FixtureInterface::load()
     */
    public function load(ObjectManager $manager)
    {
        $values = array(
            array('code' => 'EUR', 'rate' => 1),
            array('code' => 'USD', 'rate' => 1.3),
        );

        foreach ($values as $data) {
            $currency = new Currency();
            $currency->setCode($data['code']);
            $currency->setRate($data['rate']);

            $manager->persist($currency);
        }

        $manager->flush();
    }
}