<?php

namespace Lexik\Bundle\CurrencyBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig extension to format and convert currencies from templates.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class CurrencyExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Construct.
     *
     * @param ContainerInterface $container  We need the entire container to lazy load the Converter
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('currency_convert', array($this, 'convert')),
            new \Twig_SimpleFilter('currency_format', array($this, 'format')),
            new \Twig_SimpleFilter('currency_convert_format', array($this, 'convertAndFormat')),
        );
    }

    /**
     * @return \Lexik\Bundle\CurrencyBundle\Currency\ConverterInterface
     */
    public function getConverter()
    {
        return $this->container->get('lexik_currency.converter');
    }

    /**
     * @return \Lexik\Bundle\CurrencyBundle\Currency\FormatterInterface
     */
    public function getFormatter()
    {
        return $this->container->get('lexik_currency.formatter');
    }

    /**
     * Convert the given value.
     *
     * @param float   $value
     * @param string  $targetCurrency  target currency code
     * @param boolean $round      roud converted value
     * @param string  $valueCurrency   $value currency code
     * @return float
     */
    public function convert($value, $targetCurrency, $round = true, $valueCurrency = null)
    {
        return $this->getConverter()->convert($value, $targetCurrency, $round, $valueCurrency);
    }

    /**
     * Format the given value.
     *
     * @param mixed   $value
     * @param string  $valueCurrency  $value currency code
     * @param boolean $decimal        show decimal part
     * @param boolean $symbol         show currency symbol
     * @return string
     */
    public function format($value, $valueCurrency = null, $decimal = true, $symbol = true)
    {
        if (null === $valueCurrency) {
            $valueCurrency = $this->getConverter()->getDefaultCurrency();
        }

        return $this->getFormatter()->format($value, $valueCurrency, $decimal, $symbol);
    }

    /**
     * Convert and format the given value.
     *
     * @param mixed   $value
     * @param string  $targetCurrency  target currency code
     * @param boolean $decimal         show decimal part
     * @param boolean $symbol          show currency symbol
     * @param string  $valueCurrency   the $value currency code
     * @return string
     */
    public function convertAndFormat($value, $targetCurrency, $decimal = true, $symbol = true, $valueCurrency = null)
    {
        $value = $this->convert($value, $targetCurrency, $decimal, $valueCurrency);

        return $this->format($value, $targetCurrency, $decimal, $symbol);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lexik_currency.currency_extension';
    }
}
