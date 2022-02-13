<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit\Adapter;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Lexik\Bundle\CurrencyBundle\Adapter\AdapterFactory;
use Lexik\Bundle\CurrencyBundle\Adapter\DoctrineCurrencyAdapter;
use Lexik\Bundle\CurrencyBundle\Adapter\EcbCurrencyAdapter;
use Lexik\Bundle\CurrencyBundle\Adapter\YahooCurrencyAdapter;
use Lexik\Bundle\CurrencyBundle\Entity\Currency;
use Lexik\Bundle\CurrencyBundle\Tests\Unit\BaseUnitTestCase;

class AdapterFactoryTest extends BaseUnitTestCase
{
    public function setUp(): void
    {
        $this->doctrine = $this->getMockDoctrine();
        $em = $this->getEntityManager();
        $this->createSchema($em);
    }

    public function testCreateEcbAdapter(): void
    {
        $factory = new AdapterFactory($this->doctrine, 'EUR', ['EUR', 'USD'], Currency::class);
        $adapter = $factory->createEcbAdapter();

        $this->assertInstanceOf(EcbCurrencyAdapter::class, $adapter);
        $this->assertEquals('EUR', $adapter->getDefaultCurrency());
        $this->assertEquals(['EUR', 'USD'], $adapter->getManagedCurrencies());
        $this->assertEquals(0, count($adapter));
    }

    public function testCreateYahooAdapter(): void
    {
        $factory = new AdapterFactory($this->doctrine, 'EUR', ['EUR', 'USD'], Currency::class);
        $adapter = $factory->createYahooAdapter();

        $this->assertInstanceOf(YahooCurrencyAdapter::class, $adapter);
        $this->assertEquals('EUR', $adapter->getDefaultCurrency());
        $this->assertEquals(['EUR', 'USD'], $adapter->getManagedCurrencies());
        $this->assertEquals(0, count($adapter));
    }

    public function testCreateDoctrineAdapter(): void
    {
        $em = $this->getEntityManager();
        $this->loadFixtures($em);

        $factory = new AdapterFactory($this->doctrine, 'USD', ['EUR'], Currency::class);
        $adapter = $factory->createDoctrineAdapter();

        $this->assertInstanceOf(DoctrineCurrencyAdapter::class, $adapter);
        $this->assertEquals('USD', $adapter->getDefaultCurrency());
        $this->assertEquals(['EUR'], $adapter->getManagedCurrencies());
        $this->assertEquals(0, count($adapter));

        $adapter['USD']; // force initialization // @phpstan-ignore-line
        $this->assertEquals(2, count($adapter));
    }
}
