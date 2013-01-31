<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit\Twig\Extension;

use Lexik\Bundle\CurrencyBundle\Converter\Converter;
use Lexik\Bundle\CurrencyBundle\Adapter\AdapterFactory;
use Lexik\Bundle\CurrencyBundle\Twig\Extension\CurrencyExtension;
use Lexik\Bundle\CurrencyBundle\Tests\Unit\BaseUnitTestCase;

class CurrencyExtensionTest extends BaseUnitTestCase
{
    const CURRENCY_ENTITY = 'Lexik\Bundle\CurrencyBundle\Entity\Currency';

    private $em;

    private $converter;

    private $translator;

    public function setUp()
    {
        $this->em = $this->getMockSqliteEntityManager();
        $this->createSchema($this->em);
        $this->loadFixtures($this->em);

        $factory = new AdapterFactory($this->em, 'EUR', array('EUR', 'USD'), self::CURRENCY_ENTITY);

        $this->converter = new Converter($factory->createDoctrineAdapter());

        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->translator->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue('fr'));
    }

    public function testCurrencyFormat()
    {
        $extension = new CurrencyExtension($this->translator, $this->converter);

        $this->assertEquals('11,27 $', $extension->currencyFormat(8.666, 'USD'));
        $this->assertEquals('11 $', $extension->currencyFormat(8.666, 'USD', false));
        $this->assertEquals('11', $extension->currencyFormat(8.666, 'USD', false, false));
        $this->assertEquals('8,67', $extension->currencyFormat(8.666, 'USD', true, false, 'USD'));
    }
}
