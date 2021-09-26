<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Lexik\Bundle\CurrencyBundle\Entity\Currency;
use SimpleXMLElement;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class EcbCurrencyAdapter extends AbstractCurrencyAdapter
{
    private string $ecbUrl;

    public function setEcbUrl(string $url): void
    {
        $this->ecbUrl = $url;
    }

    public function attachAll(): void
    {
        $defaultRate = 1;

        /** @var Currency $euro */
        $euro = new $this->currencyClass();
        $euro->setCode('EUR');
        $euro->setRate(1);

        $this[$euro->getCode()] = $euro;

        // Get other currencies
        $xml = @simplexml_load_file($this->ecbUrl);

        if ($xml instanceof SimpleXMLElement) {
            /** @var array<int, SimpleXMLElement> $data */
            $data = $xml->xpath('//gesmes:Envelope/*[3]/*');

            /** @var SimpleXMLElement $child */
            foreach ($data[0]->children() as $child) {

                /** @var SimpleXMLElement $row */
                $row = $child->attributes();

                $code = (string) $row->currency;

                if (in_array($code, $this->managedCurrencies)) {
                    /** @var Currency $currency */
                    $currency = new $this->currencyClass();
                    $currency->setCode($code);
                    $currency->setRate((float) $row->rate);

                    $this[$currency->getCode()] = $currency;
                }
            }

            if (isset($this[$this->defaultCurrency])) {
                $defaultRate = $this[$this->defaultCurrency]->getRate();
            }

            $this->convertAll($defaultRate);
        }
    }

    public function getIdentifier(): string
    {
        return 'ecb';
    }
}
