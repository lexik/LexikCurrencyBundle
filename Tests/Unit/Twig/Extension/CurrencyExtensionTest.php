<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit\Twig\Extension;

use Lexik\Bundle\CurrencyBundle\Converter\Converter;
use Lexik\Bundle\CurrencyBundle\Adapter\AdapterFactory;
use Lexik\Bundle\CurrencyBundle\Twig\Extension\CurrencyExtension;
use Lexik\Bundle\CurrencyBundle\Tests\Unit\BaseUnitTestCase;

class CurrencyExtensionTest extends BaseUnitTestCase
{
    const CURRENCY_ENTITY = 'Lexik\Bundle\CurrencyBundle\Entity\Currency';

    private $container;
    private $em;

    private $converter;

    private $translator;

    public function setUp()
    {
        $this->container = $this->getMockContainer();
        $this->em = $this->container->get('doctrine')->getEntityManager();
        $this->createSchema($this->em);
        $this->loadFixtures($this->em);

        $factory = new AdapterFactory($this->container, null, 'EUR', array('EUR', 'USD'), self::CURRENCY_ENTITY);

        $this->converter = new Converter($factory->createDoctrineAdapter());

        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->translator->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue('fr'));
    }

    public function testConvert()
    {
        $extension = new CurrencyExtension($this->translator, $this->converter);

        $this->assertEquals(11.27, $extension->convert(8.666, 'USD'));
        $this->assertEquals(8.67, $extension->convert(8.666, 'EUR'));
    }

    public function testFormat()
    {
        $extension = new CurrencyExtension($this->translator, $this->converter);

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
        $extension = new CurrencyExtension($this->translator, $this->converter);

        $this->assertEquals('11,27 $', $extension->convertAndFormat(8.666, 'USD'));
        $this->assertEquals('11,27 $', $extension->convertAndFormat(8.666, 'USD', false));
        $this->assertEquals('11,2658', $extension->convertAndFormat(8.666, 'USD', false, false));
        $this->assertEquals('8,67', $extension->convertAndFormat(8.666, 'USD', true, false, 'USD'));
        $this->assertEquals('8', $extension->convertAndFormat(8.0, 'USD', true, false, 'USD'));
        $this->assertEquals('8,00 $', $extension->convertAndFormat(8.0, 'USD', true, true, 'USD'));
        $this->assertEquals('8 $', $extension->convertAndFormat(8.0, 'USD', false, true, 'USD'));
    }
}
