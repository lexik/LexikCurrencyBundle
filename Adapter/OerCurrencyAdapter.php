<?php

namespace Lexik\Bundle\CurrencyBundle\Adapter;

use Lexik\Bundle\CurrencyBundle\Exception\CurrencyNotFoundException;

/**
 * Oer adapter, http://openexchangerates.org
 * This provider requires registration in order to get api access
 *
 * @author Noel García <noel@coolmobile.es>
 */
class OerCurrencyAdapter extends AbstractCurrencyAdapter
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $appId;

    /**
     * Set the OER url.
     *
     * @param string $url
     */
    public function setOerUrl($url)
    {
        $this->url = $url;
    }

	/**
	 * Sets the app-id
	 *
	 * @param string $appId
	 */
	public function setOerAppId($appId)
	{
		$this->appId = $appId;
	}


    /**
     * Init object storage
     */
    public function attachAll()
    {
        // Get other currencies
        $data = @file_get_contents($this->getUrl());
		$data = @json_decode($data, true);

		if($data && is_array($data) && isset($data['rates']))
		{
			$data = $data['rates'];
			foreach($this->managedCurrencies as $code)
			{
				if(isset($data[$code]))
				{
                    $currency = new $this->currencyClass;
                    $currency->setCode($code);
                    $currency->setRate($data[$code]);

                    $this[$code] = $currency;
				}
			}

            if (isset($this[$this->defaultCurrency])) {
                $defaultRate = $this[$this->defaultCurrency]->getRate();
            }
			else
				throw new CurrencyNotFoundException("Your default currency is not supported by Oer provider");

            $this->convertAll($defaultRate);
        }
    }

	public function getUrl()
	{
		if(!$this->appId)
			throw new \InvalidArgumentException('OER_APP_ID must be set in order to use OerCurrencyAdapter');
		return sprintf("%s?app_id=%s", $this->url, $this->appId);
	}

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'oer';
    }
}