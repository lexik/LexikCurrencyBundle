<?php

namespace Lexik\Bundle\CurrencyBundle\Currency;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class Formatter implements FormatterInterface
{
    /**
     * @var array<string>
     */
    protected array $cleanCharacters;

    public function __construct(protected string $locale)
    {
        $this->cleanCharacters = ['EU', 'UK', 'US'];
    }

    public function format(float $value, string $valueCurrency = '', bool $decimal = true, bool $symbol = true): ?string
    {
        $formatter = new \NumberFormatter($this->locale, $symbol ? \NumberFormatter::CURRENCY : \NumberFormatter::PATTERN_DECIMAL);
        $value = $formatter->formatCurrency($value, $valueCurrency);

        if (!$decimal) {
            $value = (string) preg_replace('/[.,]00((?=\D)|$)/', '', $value);
        }

        if ($this->cleanCharacters !== []) {
            $value = str_replace($this->cleanCharacters, '', $value);
        }

        return $value;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @param array<string> $cleanCharacters
     */
    public function setCleanCharacters(array $cleanCharacters): void
    {
        $this->cleanCharacters = $cleanCharacters;
    }
}
