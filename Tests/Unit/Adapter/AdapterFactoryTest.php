<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit\Adapter;

use Lexik\Bundle\CurrencyBundle\Adapter\AdapterFactory;
use Lexik\Bundle\CurrencyBundle\Tests\Unit\BaseUnitTestCase;

class AdapterFactoryTest extends BaseUnitTestCase
{
    private $em;

    public function setUp()
    {
        $this->em = $this->getMockSqliteEntityManager();
        $this->createSchema($this->em);
    }

    public function testCreateEcbAdapter()
    {
        $factory = new AdapterFactory($this->em, 'EUR', array('EUR', 'USD'), 'Lexik\Bundle\CurrencyBundle\Entity\Currency');
        $adapter = $factory->createEcbAdapter();

        $this->assertInstanceOf('Lexik\Bundle\CurrencyBundle\Adapter\EcbCurrencyAdapter', $adapter);
        $this->assertEquals('EUR', $adapter->getDefaultCurrency());
        $this->assertEquals(array('EUR', 'USD'), $adapter->getManagedCurrencies());
        $this->assertEquals(0, count($adapter));
    }

    public function testCreateDoctrineAdapter()
    {
        $this->loadFixtures($this->em);

        $factory = new AdapterFactory($this->em, 'USD', array('EUR'), 'Lexik\Bundle\CurrencyBundle\Entity\Currency');
        $adapter = $factory->createDoctrineAdapter();

        $this->assertInstanceOf('Lexik\Bundle\CurrencyBundle\Adapter\DoctrineCurrencyAdapter', $adapter);
        $this->assertEquals('USD', $adapter->getDefaultCurrency());
        $this->assertEquals(array('EUR'), $adapter->getManagedCurrencies());
        $this->assertEquals(2, count($adapter));
    }
}