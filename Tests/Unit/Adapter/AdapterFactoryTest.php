<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit\Adapter;

use Lexik\Bundle\CurrencyBundle\Adapter\AdapterFactory;
use Lexik\Bundle\CurrencyBundle\Tests\Unit\BaseUnitTestCase;

class AdapterFactoryTest extends BaseUnitTestCase
{
    const CURRENCY_ENTITY = 'Lexik\Bundle\CurrencyBundle\Entity\Currency';

    protected $doctrine;

    public function setUp()
    {
        $this->doctrine = $this->getMockDoctrine();
        $em = $this->getEntityManager();
        $this->createSchema($em);
    }

    public function testCreateEcbAdapter()
    {
        $factory = new AdapterFactory($this->doctrine, 'EUR', array('EUR', 'USD'), self::CURRENCY_ENTITY);
        $adapter = $factory->createEcbAdapter();

        $this->assertInstanceOf('Lexik\Bundle\CurrencyBundle\Adapter\EcbCurrencyAdapter', $adapter);
        $this->assertEquals('EUR', $adapter->getDefaultCurrency());
        $this->assertEquals(array('EUR', 'USD'), $adapter->getManagedCurrencies());
        $this->assertEquals(0, count($adapter));
    }

    public function testCreateYahooAdapter()
    {
        $factory = new AdapterFactory($this->doctrine, 'EUR', array('EUR', 'USD'), self::CURRENCY_ENTITY);
        $adapter = $factory->createYahooAdapter();

        $this->assertInstanceOf('Lexik\Bundle\CurrencyBundle\Adapter\YahooCurrencyAdapter', $adapter);
        $this->assertEquals('EUR', $adapter->getDefaultCurrency());
        $this->assertEquals(array('EUR', 'USD'), $adapter->getManagedCurrencies());
        $this->assertEquals(0, count($adapter));
    }

    public function testCreateDoctrineAdapter()
    {
        $em = $this->getEntityManager();
        $this->loadFixtures($em);

        $factory = new AdapterFactory($this->doctrine, 'USD', array('EUR'), self::CURRENCY_ENTITY);
        $adapter = $factory->createDoctrineAdapter();

        $this->assertInstanceOf('Lexik\Bundle\CurrencyBundle\Adapter\DoctrineCurrencyAdapter', $adapter);
        $this->assertEquals('USD', $adapter->getDefaultCurrency());
        $this->assertEquals(array('EUR'), $adapter->getManagedCurrencies());
        $this->assertEquals(0, count($adapter));

        $adapter['USD']; // force initialization
        $this->assertEquals(2, count($adapter));
    }
}