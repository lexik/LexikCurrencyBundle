Overview
========

This Symfony2 bundle provide a service and a twig extension to convert and display currencies.

[![Build Status](https://secure.travis-ci.org/lexik/LexikCurrencyBundle.png?branch=master)](http://travis-ci.org/lexik/LexikCurrencyBundle)

Installation
============

Add the bunde to your `composer.json` file:

```javascript
require: {
    // ...
    "lexik/currency-bundle": "v1.1.*"
    // ...
}
```

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
        default: EUR             # [required] the default currency
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
    default_adapter: doctrine_currency_adapter  # service id OR tag alias
```

Initialize currencies
=====================

To initialize the currencies rate in the database run the following command:

```
./app/console lexik:currency:import <currency adapter identifier>
```

Example by using the ECB adapter, to get rates from the European Central Bank.
In the commanda line `ecb` is the value returned by the `getIdentifier()` method of the adapter class.

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

##### Twig filters

The bundle provide 3 filters to convert and format a value:
* `currency_convert`: convert a value.
* `currency_format`: format a value according to the current locale.
* `currency_modify`: convert and format a value.

**The `currency_modify` was formerly named `currency_format` when this filter was the only one provided by the bundle.**

Here an example with the `currency_modify` filter.

```
{% set targetCurrency = 'EUR' %}
{{ amount | currency_modify(targetCurrency) }}
```

You can also pass more arguments, to display or not decimal and the currency symbol. And you can specify the amount's currency if needed.

```
{% set targetCurrency = 'EUR' %}
{% set amountCurrency = 'USD' %}
{% set decimal = false %}
{% set symbol = true %}

{{ amount | currency_modify(targetCurrency, decimal, symbol, amountCurrency) }}
```
