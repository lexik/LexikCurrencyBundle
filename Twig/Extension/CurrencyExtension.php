<?php

namespace Lexik\Bundle\CurrencyBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\Container;
use Lexik\Bundle\CurrencyBundle\Converter\Converter;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Twig extension to format and convert currencies from templates.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class CurrencyExtension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Construct.
     *
     * @param TranslatorInterface $translator
     * @param Container           $container  We need the entire container to lazy load the Converter
     */
    public function __construct(TranslatorInterface $translator, Container $container)
    {
        $this->translator = $translator;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'currency_convert'        => new \Twig_Filter_Method($this, 'convert'),
            'currency_format'         => new \Twig_Filter_Method($this, 'format'),
            'currency_convert_format' => new \Twig_Filter_Method($this, 'convertAndFormat'),
        );
    }

    /**
     * Return Currency Converter
     *
     * @return Converter
     */
    public function getConverter()
    {
        return $this->container->get('lexik_currency.converter');
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

        $formatter = new \NumberFormatter($this->translator->getLocale(), $symbol ? \NumberFormatter::CURRENCY : \NumberFormatter::PATTERN_DECIMAL);
        $value = $formatter->formatCurrency($value, $valueCurrency);

        if (!$decimal) {
            $value = preg_replace('/[.,]00((?=\D)|$)/', '', $value);
        }

        $value = str_replace(array('EU', 'UK', 'US'), '', $value);

        return $value;
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
