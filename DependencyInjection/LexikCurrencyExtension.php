<?php

namespace Lexik\Bundle\CurrencyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 */
class LexikCurrencyExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('adapters.xml');

        $container->setParameter('lexik_currency.currencies.default', $config['currencies']['default']);
        $container->setParameter('lexik_currency.currency_class', $config['currency_class']);
        $container->setParameter('lexik_currency.default_adapter', $config['default_adapter']);
        $container->setParameter('lexik_currency.ecb_url', $config['ecb_url']);
        $container->setParameter('lexik_currency.oer_url', $config['oer_url']);
        $container->setParameter('lexik_currency.oer_app_id', $config['oer_app_id']);
        $container->setParameter('lexik_currency.yahoo_url', $config['yahoo_url']);
        $container->setParameter('lexik_currency.decimal_part.precision', $config['decimal_part']['precision']);
        $container->setParameter('lexik_currency.decimal_part.round_mode', $config['decimal_part']['round_mode']);

        // Add default currency to managed currencies if needed
        if (!in_array($config['currencies']['default'], $config['currencies']['managed'])) {
            $config['currencies']['managed'][] = $config['currencies']['default'];
        }

        $container->setParameter('lexik_currency.currencies.managed', $config['currencies']['managed']);
    }
}
