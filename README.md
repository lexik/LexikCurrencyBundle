Overview
========

This Symfony2 bundle provide a service and a twig extension to convert and display currencies.

[![Build Status](https://secure.travis-ci.org/lexik/LexikCurrencyBundle.png?branch=master)](http://travis-ci.org/lexik/LexikCurrencyBundle)
[![Latest Stable Version](https://poser.pugx.org/lexik/currency-bundle/v/stable)](https://packagist.org/packages/lexik/currency-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/04079218-2ad1-439d-bfab-1c931468147c/mini.png)](https://insight.sensiolabs.com/projects/04079218-2ad1-439d-bfab-1c931468147c)

Installation
============

Add the bunde to your `composer.json` file:

```javascript
require: {
    // ...
    "lexik/currency-bundle": "~2.0"
    // ...
}
```

**As of version `1.2.0`, `currency_format` does not convert the currency anymore, it only formats the given value according to the locale. If you need to convert and format a value, please use `currency_convert_format` filter.**

Then run a composer update:

```shell
composer.phar update
# OR
composer.phar update lexik/currency-bundle # to only update the bundle
```

Register the bundle with your kernel:

```
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Lexik\Bundle\CurrencyBundle\LexikCurrencyBundle(),
    // ...
);
```

Configuration
=============

Minimun configuration:

```yaml
# app/config/config.yml
lexik_currency:
    currencies:
        default: EUR              # [required] the default currency
        managed: [EUR, USD, ...]  # [required] all currencies used in your app
```

Additonal options (default values are shown here):

```yaml
# app/config/config.yml
lexik_currency:
    decimal_part:
        precision:  2                           # number of digits for the decimal part
        round_mode: up                          # round mode to use (up|down|even|odd)
	currency_class: Lexik\Bundle\CurrencyBundle\Entity\Currency  # Use your custom Currency Entity
    default_adapter: doctrine_currency_adapter  # service id OR tag alias, this is adapter used by the conversion service
```

Initialize currencies
=====================

To initialize the currencies rate in the database run the following command:

```
./app/console lexik:currency:import <currency adapter identifier>
```

Example by using the ECB adapter, to get rates from the European Central Bank.
In the command line `ecb` is the value returned by the `getIdentifier()` method of the adapter class.

```
./app/console lexik:currency:import ecb
```

Usage
=====

##### Currency conversion service

Use the `convert()` method from the `lexik_currency.converter` service:

```php
<?php
// by default the amount will rounded and the amount have to be in the default currency
$convertedAmount = $container->get('lexik_currency.converter')->convert($amount, $targetCurrency);

// here the amount won't be rounded and we specify that $amount currency is 'USD'
$convertedAmount = $container->get('lexik_currency.converter')->convert($amount, $targetCurrency, false, 'USD');
```

##### Retrieve managed configurations

In the controller, you can use the following line to retrieve an array of all managed currencies:

```php
$managedCurrencies = $this->container->getParameter('lexik_currency.currencies.managed');
```

##### Twig filters

The bundle provide 3 filters to convert and format a value:
* `currency_convert`: convert a value.
* `currency_format`: format a value according to the current locale.
* `currency_convert_format`: convert and format a value.

Here an example with the `currency_convert_format` filter.

```
{% set targetCurrency = 'EUR' %}
{{ amount | currency_convert_format(targetCurrency) }}
```

You can also pass more arguments, to display or not decimal and the currency symbol. And you can specify the amount's currency if needed.

```
{% set targetCurrency = 'EUR' %}
{% set amountCurrency = 'USD' %}
{% set decimal = false %}
{% set symbol = true %}

{{ amount | currency_convert_format(targetCurrency, decimal, symbol, amountCurrency) }}
```

##### Load conversions rate from another source (custom CurrencyAdatpter)

If you need to load conversions rates from another source you will have to create a CurrencyAdatpter and set it as the default adapter.

To create your custom adapter you will have to extend `Lexik\Bundle\CurrencyBundle\Adapte\AbstractCurrencyAdapter` which define 2 abstract methods:
* getIdentifier(): returns the identifier of the adapter.
* attachAll(): loads the currencies with their rate (this method is call from the import command to get all currencies to save in the database).

Here an example

```php
<?php

namespace MyProject\With\Some\Rainbows;

use Lexik\Bundle\CurrencyBundle\Adapter\AbstractCurrencyAdapter;

class RainbowCurrencyAdapter extends AbstractCurrencyAdapter
{
	/**
     * {@inheritdoc}
     */
    public function attachAll()
    {
    	$defaultRate = 1;

        // Add default currency (euro in this example)
        $euro = new $this->currencyClass;
        $euro->setCode('EUR');
        $euro->setRate($defaultRate);

        $this[$euro->getCode()] = $euro;

        // Get other currencies
        $currencies = // get all currencies with their rate (from a file, an url, etc)

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
        return 'rainbow';
    }
}
```

Then define the adapter as a service, don't forget the `lexik_currency.adapter` tag:

```xml
<service id="my_project.rainbow_currency_adapter" class="MyProject\With\Some\Rainbows\RainbowCurrencyAdapter">
    <call method="setDefaultCurrency">
        <argument>%lexik_currency.currencies.default%</argument>
    </call>
    <call method="setManagedCurrencies">
        <argument>%lexik_currency.currencies.managed%</argument>
    </call>
    <call method="setCurrencyClass">
        <argument>%lexik_currency.currency_class%</argument>
    </call>
    <tag name="lexik_currency.adapter" alias="rainbow_currency_adapter" />
</service>
```

And import the currencies by using your adapter:

```
./app/console lexik:currency:import rainbow
```
