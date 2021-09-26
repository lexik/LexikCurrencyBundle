<?php

namespace Lexik\Bundle\CurrencyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class AdapterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Attach all adapter to the adapter collector
        $definition = $container->getDefinition('lexik_currency.adapter_collector');

        foreach (array_keys($container->findTaggedServiceIds('lexik_currency.adapter')) as $id) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }

        // set default adapter alias
        $defaultAdapter = $container->getParameter('lexik_currency.default_adapter');

        foreach ($container->findTaggedServiceIds('lexik_currency.adapter') as $id => $attributes) {
            if ($defaultAdapter === $id || $defaultAdapter === $attributes[0]['alias']) {
                $container->setAlias('lexik_currency.default_adapter', $id);
            }
        }
    }
}
