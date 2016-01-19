<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Lexik\Bundle\CurrencyBundle\Exception\CurrencyNotFoundException;

/**
 * Yahoo! Adapter
 *
 * @author Jonas Dambacher <jonas.dambacher@coffee-bike.com>
 */
class YahooCurrencyAdapter extends AbstractCurrencyAdapter
{
    /**
     * @var string
     */
    private $yahooUrl;

    /**
     * @var array
     */
    private $currencyCodes = array();


    /**
     * Set the Yahoo! url.
     *
     * @param string $url
     */
    public function setYahooUrl($url)
    {
        $this->yahooUrl = $url;
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
        $euro = new $this->currencyClass;
        $euro->setCode('EUR');
        $euro->setRate($defaultRate);

        $this[$euro->getCode()] = $euro;

        // Build YQL query
        $strCodes = '';
        foreach ($this->currencyCodes as $index=>$currencyCode) {
            $strCodes .= "'EUR".$currencyCode."'";
            if ($index != count($this->currencyCodes) - 1) {
                $strCodes .= ", ";
            }
        }

        $yqlQuery = "select id,Rate from yahoo.finance.xchange where pair in (".$strCodes.")";

        $args = array(
            'q' => $yqlQuery,
            'format' => "json",
            'env' => "store://datatables.org/alltableswithkeys",
        );

        $yqlQueryURL = $this->yahooUrl
            . "?q=" . urlencode($yqlQuery)
            . "&format=json"
            . "&env=store://datatables.org/alltableswithkeys";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $yqlQueryURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $json = curl_exec($ch);

        // Convert JSON response to PHP object
        $data = json_decode($json);
        $results = $data->query->results->rate;

        // Check if query was okay and result is given
        if (is_null($results)) {
            new \RuntimeException('YQL query failed!');
        }

        $currencies = array();

        foreach ($results as $row) {
            $code = substr($row->id, 3);
            $rate = $row->Rate;

            $currencies[$code] = $rate;
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
        return 'yahoo';
    }

    /**
     * Add currency to the query
     *
     * @param $code
     */
    private function addCurrency($code) {
        $this->currencyCodes[] = $code;
    }

}