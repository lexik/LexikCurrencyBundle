<?php

namespace Lexik\Bundle\CurrencyBundle\Twig\Extension;

use Lexik\Bundle\CurrencyBundle\Adapter\AdapterCollector;
use Lexik\Bundle\CurrencyBundle\Converter\Converter;
use Lexik\Bundle\CurrencyBundle\Exception\CurrencyNotFoundException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class CurrencyExtension extends \Twig_Extension
{
    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Construct.
     *
     * @param TranslatorInterface $translator
     * @param Converter $converter
     */
    public function __construct(TranslatorInterface $translator, Converter $converter)
    {
        $this->translator = $translator;
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'currency_format'   => new \Twig_Filter_Method($this, 'currencyFormat'),
        );
    }

    /**
     * Format price
     *
     * @param mixed   $value
     * @param string  $code      target currency code
     * @param boolean $decimal   show decimal part
     * @param boolean $symbol    show currency symbol
     * @param string  $valueCode the $value currency code
     * @return string
     */
    public function currencyFormat($value, $code, $decimal = true, $symbol = true, $valueCode = null)
    {
        try {
            $value = $this->converter->convert($value, $code, true, $valueCode);
        } catch (CurrencyNotFoundException $e) {
            $code = $this->converter->getDefaultCurrency();
        }

        $formatter = new \NumberFormatter($this->translator->getLocale(), $symbol ? \NumberFormatter::CURRENCY : \NumberFormatter::PATTERN_DECIMAL);
        $value = $formatter->formatCurrency($value, $code);

        if (!$decimal) {
            $value = preg_replace('/[.,][0-9]*((?=\D)|$)/', '', $value);
        }

        $value = str_replace(array('EU', 'UK', 'US'), '', $value);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lexik_currency.currency_extension';
    }
}
