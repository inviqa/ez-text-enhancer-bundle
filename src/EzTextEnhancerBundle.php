<?php

namespace Inviqa\EzTextEnhancerBundle;

use Inviqa\EzTextEnhancerBundle\DependencyInjection\CompilerPass\TextEnhancerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzTextEnhancerBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TextEnhancerCompilerPass());
    }
}
