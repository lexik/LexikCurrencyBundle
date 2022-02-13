<?php

namespace Lexik\Bundle\CurrencyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('lexik_currency');
        $rootNode = $treeBuilder->getRootNode();

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
                            ->defaultValue(['EUR'])
                            ->isRequired()
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('decimal_part')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('precision')
                            ->defaultValue(2)
                        ->end()
                        ->scalarNode('round_mode')
                            ->defaultValue('up')
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('currency_class')
                    ->cannotBeEmpty()
                    ->defaultValue('Lexik\Bundle\CurrencyBundle\Entity\Currency')
                ->end()

                ->scalarNode('default_adapter')
                    ->cannotBeEmpty()
                    ->defaultValue('doctrine_currency_adapter') // service id OR tag alias
                ->end()

                ->scalarNode('ecb_url')
                    ->cannotBeEmpty()
                    ->defaultValue('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml')
                ->end()

                ->scalarNode('oer_url')
                    ->cannotBeEmpty()
                    ->defaultValue('http://openexchangerates.org/api/latest.json')
                ->end()

                ->scalarNode('oer_app_id')
                    ->defaultValue(null)
                ->end()

                ->scalarNode('yahoo_url')
                    ->cannotBeEmpty()
                    ->defaultValue('https://query.yahooapis.com/v1/public/yql')
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
