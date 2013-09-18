<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit\Twig\Extension;

use Lexik\Bundle\CurrencyBundle\Converter\Converter;
use Lexik\Bundle\CurrencyBundle\Adapter\AdapterFactory;
use Lexik\Bundle\CurrencyBundle\Twig\Extension\CurrencyExtension;
use Lexik\Bundle\CurrencyBundle\Tests\Unit\BaseUnitTestCase;
use Symfony\Component\DependencyInjection\Container;

class CurrencyExtensionTest extends BaseUnitTestCase
{
    const CURRENCY_ENTITY = 'Lexik\Bundle\CurrencyBundle\Entity\Currency';

    protected $doctrine;

    private $container;

    private $translator;

    public function setUp()
    {
        $this->doctrine = $this->getMockDoctrine();
        $em = $this->getEntityManager();

        $this->createSchema($em);
        $this->loadFixtures($em);

        $factory = new AdapterFactory($this->doctrine, 'EUR', array('EUR', 'USD'), self::CURRENCY_ENTITY);

        $converter = new Converter($factory->createDoctrineAdapter());

        $this->container = new Container();
        $this->container->set('lexik_currency.converter', $converter);

        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->translator->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue('fr'));
    }

    public function testConvert()
    {
        $extension = new CurrencyExtension($this->translator, $this->container);

        $this->assertEquals(11.27, $extension->convert(8.666, 'USD'));
        $this->assertEquals(8.67, $extension->convert(8.666, 'EUR'));
    }

    public function testFormat()
    {
        $extension = new CurrencyExtension($this->translator, $this->container);

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
        $extension = new CurrencyExtension($this->translator, $this->container);

        $this->assertEquals('11,27 $', $extension->convertAndFormat(8.666, 'USD'));
        $this->assertEquals('11,27 $', $extension->convertAndFormat(8.666, 'USD', false));
        $this->assertEquals('11,2658', $extension->convertAndFormat(8.666, 'USD', false, false));
        $this->assertEquals('8,67', $extension->convertAndFormat(8.666, 'USD', true, false, 'USD'));
        $this->assertEquals('8', $extension->convertAndFormat(8.0, 'USD', true, false, 'USD'));
        $this->assertEquals('8,00 $', $extension->convertAndFormat(8.0, 'USD', true, true, 'USD'));
        $this->assertEquals('8 $', $extension->convertAndFormat(8.0, 'USD', false, true, 'USD'));
    }
}
