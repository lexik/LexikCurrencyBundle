<?php

namespace Lexik\Bundle\CurrencyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lexik_currency');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('currencies')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')
                            ->cannotBeEmpty()
                            ->defaultValue('EUR')
                            ->isRequired()
                        ->end()

                        ->arrayNode('managed')
                            ->defaultValue(array('EUR'))
                            ->isRequired()
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('default_adapter')
                    ->cannotBeEmpty()
                    ->defaultValue('doctrine_currency_adapter') // service id OR tag alias
                ->end()

                ->scalarNode('ecb_url')
                    ->cannotBeEmpty()
                    ->defaultValue('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
