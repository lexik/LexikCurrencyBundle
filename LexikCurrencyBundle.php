<?php

namespace Lexik\Bundle\CurrencyBundle;

use Lexik\Bundle\CurrencyBundle\DependencyInjection\Compiler\AdapterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LexikCurrencyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        
        $container->addCompilerPass(new AdapterPass());
    }
}
