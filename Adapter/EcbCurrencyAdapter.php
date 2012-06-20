<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class EcbCurrencyAdapter extends AbstractCurrencyAdapter
{
    /**
     * @var string
     */
    private $ecbUrl;

    /**
     * Set the ECB url.
     *
     * @param string $url
     */
    public function setEcbUrl($url)
    {
        $this->ecbUrl = $url;
    }

    /**
     * Init object storage
     */
    public function attachAll()
    {
        $defaultRate = 1;

        // Add euro
        $euro = new $this->currencyClass;
        $euro->setCode('EUR');
        $euro->setRate(1);

        $this[$euro->getCode()] = $euro;

        // Get other currencies
        // @todo timeout + try catch
        $xml = @simplexml_load_file($this->ecbUrl);

        if ($xml instanceof \SimpleXMLElement) {
            $crawler = new Crawler($xml->asXML());
            $datas = $crawler
                ->filter('cube > cube > cube')
                ->extract(array('currency', 'rate'));

            foreach ($datas as $data) {
                if (in_array($data[0], $this->managedCurrencies)) {
                    $currency = new $this->currencyClass;
                    $currency->setCode($data[0]);
                    $currency->setRate($data[1]);

                    $this[$currency->getCode()] = $currency;
                }
            }

            if (isset($this[$this->defaultCurrency])) {
                $defaultRate = $this[$this->defaultCurrency]->getRate();
            }

            $this->convertAll($defaultRate);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'ecb';
    }
}