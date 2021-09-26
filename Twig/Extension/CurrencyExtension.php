<?php

namespace Lexik\Bundle\CurrencyBundle\Twig\Extension;

use Lexik\Bundle\CurrencyBundle\Currency\ConverterInterface;
use Lexik\Bundle\CurrencyBundle\Currency\FormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to format and convert currencies from templates.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class CurrencyExtension extends AbstractExtension
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('currency_convert', [$this, 'convert']),
            new TwigFilter('currency_format', [$this, 'format']),
            new TwigFilter('currency_convert_format', [$this, 'convertAndFormat']),
        ];
    }

    public function getConverter(): ConverterInterface
    {
        $converter = $this->container->get('lexik_currency.converter');
        assert($converter instanceof ConverterInterface);

        return $converter;
    }

    public function getFormatter(): FormatterInterface
    {
        $formatter = $this->container->get('lexik_currency.formatter');
        assert($formatter instanceof FormatterInterface);

        return $formatter;
    }

    public function convert(float $value, string $targetCurrency, bool $round = true, string $valueCurrency = null): float
    {
        return $this->getConverter()->convert($value, $targetCurrency, $round, $valueCurrency);
    }

    public function format(float $value, string $valueCurrency = null, bool $decimal = true, bool $symbol = true): ?string
    {
        if (null === $valueCurrency) {
            $valueCurrency = $this->getConverter()->getDefaultCurrency();
        }

        return $this->getFormatter()->format($value, $valueCurrency, $decimal, $symbol);
    }

    public function convertAndFormat(float $value, string $targetCurrency, bool $decimal = true, bool $symbol = true, string $valueCurrency = null): ?string
    {
        $value = $this->convert($value, $targetCurrency, $decimal, $valueCurrency);

        return $this->format($value, $targetCurrency, $decimal, $symbol);
    }

    public function getName(): string
    {
        return 'lexik_currency.currency_extension';
    }
}
