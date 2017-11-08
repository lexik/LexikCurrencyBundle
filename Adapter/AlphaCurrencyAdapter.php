<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Lexik\Bundle\CurrencyBundle\Exception\CurrencyNotFoundException;

/**
 * Alpha Vantage Currency Adapter
 *
 * @author Jonas Dambacher <jonas.dambacher@coffee-bike.com>
 */
class AlphaCurrencyAdapter extends AbstractCurrencyAdapter
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var array
     */
    private $currencyCodes = array();

    /**
     * Set the Alpha Vantage API key.
     *
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Init object storage
     */
    public function attachAll()
    {
        foreach ($this->managedCurrencies as $managedCurrency) {
            $this->addCurrency($managedCurrency);
        }

        $defaultRate = 1;

        // Add default currency (euro in this example)
        $defaultCurrency = new $this->currencyClass;
        $defaultCurrency->setCode('EUR');
        $defaultCurrency->setRate($defaultRate);

        $this[$defaultCurrency->getCode()] = $defaultCurrency;

        $currencies = [];
        // Build query
        foreach ($this->currencyCodes as $index=>$currencyCode) {
            $currencies[$currencyCode] = $this->getExchangeRate($currencyCode);
        }

        foreach ($currencies as $code => $rate) {
            if (in_array($code, $this->managedCurrencies)) { // you can check if the currency is in the managed currencies
                $currency = new $this->currencyClass;
                $currency->setCode($code);
                $currency->setRate($rate);

                $this[$currency->getCode()] = $currency;
            }
        }

        // get the default rate from the default currency defined in the configuration
        if (isset($this[$this->defaultCurrency])) {
            $defaultRate = $this[$this->defaultCurrency]->getRate();
        }

        // convert rates according to the default one.
        $this->convertAll($defaultRate);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'alpha';
    }

    /**
     * Add currency to the query
     *
     * @param $code
     */
    private function addCurrency($code)
    {
        $this->currencyCodes[] = $code;
    }

    /**
     * @param $currency
     * @return float
     */
    private function getExchangeRate($currency)
    {
        if (!$this->apiKey) {
            throw new \InvalidArgumentException('ALPHA_API_KEY must be set in order to use AlphaCurrencyAdapter');
        }

        $url = sprintf('https://www.alphavantage.co/query?function=CURRENCY_EXCHANGE_RATE&from_currency=%s&to_currency=%s&apikey=%s', $this->defaultCurrency, $currency, $this->apiKey);
        $json = file_get_contents($url);
        $data = json_decode($json, true);

        return $data['Realtime Currency Exchange Rate']['5. Exchange Rate'];
    }

}