<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit\Adapter;

use Lexik\Bundle\CurrencyBundle\Adapter\AdapterFactory;
use Lexik\Bundle\CurrencyBundle\Tests\Unit\BaseUnitTestCase;

class AdapterFactoryTest extends BaseUnitTestCase
{
    const CURRENCY_ENTITY = 'Lexik\Bundle\CurrencyBundle\Entity\Currency';

    private $container;
    private $em;

    public function setUp()
    {
        $this->container = $this->getMockContainer();
        $this->em = $this->container->get('doctrine')->getEntityManager();
        $this->createSchema($this->em);
    }

    public function testCreateEcbAdapter()
    {
        $factory = new AdapterFactory($this->container, null, 'EUR', array('EUR', 'USD'), self::CURRENCY_ENTITY);
        $adapter = $factory->createEcbAdapter();

        $this->assertInstanceOf('Lexik\Bundle\CurrencyBundle\Adapter\EcbCurrencyAdapter', $adapter);
        $this->assertEquals('EUR', $adapter->getDefaultCurrency());
        $this->assertEquals(array('EUR', 'USD'), $adapter->getManagedCurrencies());
        $this->assertEquals(0, count($adapter));
    }

    public function testCreateDoctrineAdapter()
    {
        $this->loadFixtures($this->em);

        $factory = new AdapterFactory($this->container, null, 'USD', array('EUR'), self::CURRENCY_ENTITY);
        $adapter = $factory->createDoctrineAdapter();

        $this->assertInstanceOf('Lexik\Bundle\CurrencyBundle\Adapter\DoctrineCurrencyAdapter', $adapter);
        $this->assertEquals('USD', $adapter->getDefaultCurrency());
        $this->assertEquals(array('EUR'), $adapter->getManagedCurrencies());
        $this->assertEquals(2, count($adapter));
    }
}