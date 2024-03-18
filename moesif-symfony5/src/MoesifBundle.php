<?php

namespace Moesif\MoesifBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Moesif\MoesifBundle\DependencyInjection\MoesifExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface; // Add this line

class MoesifBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        
        // Register extensions or compile passes if you have any.
        // $container->addCompilerPass(new YourCompilerPass());
    }

    /**
     * Overriding the getContainerExtension method to specify the return type.
     *
     * @return ExtensionInterface|null
     */
    public function getContainerExtension(): ?ExtensionInterface // Specify the return type
    {
        if (null === $this->extension) {
            $this->extension = new MoesifExtension();
        }

        return $this->extension;
    }
}
