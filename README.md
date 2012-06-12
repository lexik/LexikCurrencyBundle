Overview
========

This Symfony2 bundle provide a service and a twig extension to convert and display currencies.


Installation
============

Update your `deps` and `deps.lock` files:

```
// deps
...
[LexikCurrencyBundle]
    git=https://github.com/lexik/LexikCurrencyBundle.git
    target=bundles/Lexik/Bundle/CurrencyBundle
```

```
// deps.lock
...
LexikCurrencyBundle <commit>
```

Register the namespaces with the autoloader:

```
// app/autoload.php
 $loader->registerNamespaces(array(
    // ...
    'Lexik' => __DIR__.'/../vendor/bundles',
    // ...
));
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

This is the full configartion tree with the default values:

```yaml
# app/config/config.yml
lexik_currency:
    currencies:
        default: EUR                            # [required] the default currency
        managed: [EUR]                          # [required] all currencies used in your app
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

##### In a twig template

Just use the `currency_format` filter:

```
{% set targetCurrency = 'EUR' %}
{{ amount | currency_format(targetCurrency)  }}
```

You can also pass more arguments, to display or not decimal and the currency symbol. And you can specify the amount's currency if needed.

```
{% set targetCurrency = 'EUR' %}
{% set amountCurrency = 'USD' %}
{% set decimal = false  %}
{% set symbol = true %}

{{ amount | currency_format(targetCurrency, decimal, symbol, amountCurrency)  }}
```

##### By using the service

Use the `convert()` method from the `lexik_currency.converter` service:

```php
<?php
// by default the amount will rounded and the amount have to be in the default currency
$convertedAmount = $container->get('lexik_currency.converter')->convert($amount, $targetCurrency);

// here the amount won't be rounded and we specify that $amount currency is 'USD'
$convertedAmount = $container->get('lexik_currency.converter')->convert($amount, $targetCurrency, false, 'USD');
```

