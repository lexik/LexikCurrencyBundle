<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit\Converter;

use Lexik\Bundle\CurrencyBundle\Adapter\AdapterFactory;
use Lexik\Bundle\CurrencyBundle\Currency\Converter;
use Lexik\Bundle\CurrencyBundle\Tests\Unit\BaseUnitTestCase;

class ConverterTest extends BaseUnitTestCase
{
    const CURRENCY_ENTITY = 'Lexik\Bundle\CurrencyBundle\Entity\Currency';

    protected $doctrine;

    private $adapter;

    public function setUp()
    {
        $this->doctrine = $this->getMockDoctrine();
        $em = $this->getEntityManager();

        $this->createSchema($em);
        $this->loadFixtures($em);

        $factory = new AdapterFactory($this->doctrine, 'EUR', array('EUR', 'USD'), self::CURRENCY_ENTITY);
        $this->adapter = $factory->createDoctrineAdapter();
    }

    public function testConvert()
    {
        $converter = new Converter($this->adapter);

        $this->assertEquals(11.27, $converter->convert(8.666, 'USD'));
        $this->assertEquals(8.67, $converter->convert(8.666, 'EUR'));

        $converter = new Converter($this->adapter, 3);

        $this->assertEquals(11.266, $converter->convert(8.666, 'USD'));
        $this->assertEquals(8.666, $converter->convert(8.666, 'EUR'));
    }

    public function testConvertNotRounded()
    {
        $converter = new Converter($this->adapter);

        $this->assertEquals(11.2658, $converter->convert(8.666, 'USD', false));
        $this->assertEquals(8.666, $converter->convert(8.666, 'EUR', false));
    }

    public function testConvertFromNoDefaultCurrency()
    {
        $converter = new Converter($this->adapter);

        $this->assertEquals(8.67, $converter->convert(8.666, 'USD', true, 'USD'));
        $this->assertEquals(6.67, $converter->convert(8.666, 'EUR', true, 'USD'));
    }

    public function testConvertFromNoDefaultCurrencyNotRounded()
    {
        $converter = new Converter($this->adapter);

        $this->assertEquals(8.666, $converter->convert(8.666, 'USD', false, 'USD'));
        $this->assertEquals(6.6661538461538, $converter->convert(8.666, 'EUR', false, 'USD'));
    }

    /**
     * @expectedException        Lexik\Bundle\CurrencyBundle\Exception\CurrencyNotFoundException
     * @expectedExceptionMessage Can't find currency: "UUU"
     */
    public function testConvertUndefinedTarget()
    {
        $converter = new Converter($this->adapter);

        $converter->convert(8.666, 'UUU');
    }
}