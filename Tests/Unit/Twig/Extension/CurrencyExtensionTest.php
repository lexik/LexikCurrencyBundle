<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit\Twig\Extension;

use Lexik\Bundle\CurrencyBundle\Currency\Converter;
use Lexik\Bundle\CurrencyBundle\Adapter\AdapterFactory;
use Lexik\Bundle\CurrencyBundle\Currency\Formatter;
use Lexik\Bundle\CurrencyBundle\Twig\Extension\CurrencyExtension;
use Lexik\Bundle\CurrencyBundle\Tests\Unit\BaseUnitTestCase;
use Symfony\Component\DependencyInjection\Container;

class CurrencyExtensionTest extends BaseUnitTestCase
{
    const CURRENCY_ENTITY = 'Lexik\Bundle\CurrencyBundle\Entity\Currency';

    protected $doctrine;

    private $container;

    public function setUp()
    {
        $this->doctrine = $this->getMockDoctrine();
        $em = $this->getEntityManager();

        $this->createSchema($em);
        $this->loadFixtures($em);

        $factory = new AdapterFactory($this->doctrine, 'EUR', array('EUR', 'USD'), self::CURRENCY_ENTITY);

        $converter = new Converter($factory->createDoctrineAdapter());

        $formatter = new Formatter('fr');

        $this->container = new Container();
        $this->container->set('lexik_currency.converter', $converter);
        $this->container->set('lexik_currency.formatter', $formatter);
    }

    public function testConvert()
    {
        $extension = new CurrencyExtension($this->container);

        $this->assertEquals(11.27, $extension->convert(8.666, 'USD'));
        $this->assertEquals(8.67, $extension->convert(8.666, 'EUR'));
    }

    public function testFormat()
    {
        $extension = new CurrencyExtension($this->container);

        $this->assertEquals('8,67 €', $extension->format(8.666));
        $this->assertEquals('8,67 €', $extension->format(8.666, 'EUR'));
        $this->assertEquals('8,67 $', $extension->format(8.666, 'USD'));
        $this->assertEquals('8 $', $extension->format(8.0, 'USD', false));
        $this->assertEquals('8,666', $extension->format(8.666, 'USD', false, false));
        $this->assertEquals('8', $extension->format(8.0, 'USD', true, false));
        $this->assertEquals('8 $', $extension->format(8.0, 'USD', false, true));
    }

    public function testConvertAndFormat()
    {
        $extension = new CurrencyExtension($this->container);

        $this->assertEquals('11,27 $', $extension->convertAndFormat(8.666, 'USD'));
        $this->assertEquals('11,27 $', $extension->convertAndFormat(8.666, 'USD', false));
        $this->assertEquals('11,2658', $extension->convertAndFormat(8.666, 'USD', false, false));
        $this->assertEquals('8,67', $extension->convertAndFormat(8.666, 'USD', true, false, 'USD'));
        $this->assertEquals('8', $extension->convertAndFormat(8.0, 'USD', true, false, 'USD'));
        $this->assertEquals('8,00 $', $extension->convertAndFormat(8.0, 'USD', true, true, 'USD'));
        $this->assertEquals('8 $', $extension->convertAndFormat(8.0, 'USD', false, true, 'USD'));
    }
}
