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
        $container->setParameter('lexik_currency.default_adapter', $config['default_adapter']);
        $container->setParameter('lexik_currency.ecb_url', $config['ecb_url']);

        // Add default currency to managed currencies if needed
        if (!in_array($config['currencies']['default'], $config['currencies']['managed'])) {
            $config['currencies']['managed'][] = $config['currencies']['default'];
        }

        $container->setParameter('lexik_currency.currencies.managed', $config['currencies']['managed']);

        // set default adapter alias
        foreach ($container->findTaggedServiceIds('lexik_currency.adapter') as $id => $attributes) {
            if ($config['default_adapter'] == $id || $config['default_adapter'] == $attributes[0]['alias']) {
                $container->setAlias('lexik_currency.default_adapter', $id);
            }
        }
    }
}
