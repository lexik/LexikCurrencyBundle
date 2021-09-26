<?php

namespace Lexik\Bundle\CurrencyBundle\Currency;

use Lexik\Bundle\CurrencyBundle\Adapter\AbstractCurrencyAdapter;
use Lexik\Bundle\CurrencyBundle\Exception\CurrencyNotFoundException;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class Converter implements ConverterInterface
{
    private int $roundMode;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(protected AbstractCurrencyAdapter $adapter, protected int $precision = 2, string $roundMode = 'up')
    {
        $allowedModes = ['up', 'down', 'even', 'odd'];

        if (!in_array($roundMode, $allowedModes)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid round mode "%s", please use one of the following values: %s',
                    $roundMode,
                    implode(', ', $allowedModes)
                )
            );
        }

        /** @var int $roundingConstant */
        $roundingConstant = constant(sprintf('PHP_ROUND_HALF_%s', strtoupper($roundMode)));
        $this->roundMode = $roundingConstant;
    }

    public function convert(float $value, string $targetCurrency, bool $round = true, string $valueCurrency = null): float
    {
        if (!isset($this->adapter[$targetCurrency])) {
            throw new CurrencyNotFoundException($targetCurrency);
        }

        if (null == $valueCurrency) {
            $valueCurrency = $this->getDefaultCurrency();
        }

        if (!isset($this->adapter[$valueCurrency])) {
            throw new CurrencyNotFoundException($valueCurrency);
        }

        if ($targetCurrency !== $valueCurrency) {
            if ($this->getDefaultCurrency() === $valueCurrency) {
                $value *= $this->adapter[$targetCurrency]->getRate();
            } else {
                $value /= $this->adapter[$valueCurrency]->getRate(); // value in the default currency

                if ($this->getDefaultCurrency() !== $targetCurrency) {
                    $value *= $this->adapter[$targetCurrency]->getRate();
                }
            }
        }

        return $round ? round($value, $this->precision, (int) $this->roundMode) : $value;
    }

    public function getDefaultCurrency(): string
    {
        return $this->adapter->getDefaultCurrency();
    }
}
