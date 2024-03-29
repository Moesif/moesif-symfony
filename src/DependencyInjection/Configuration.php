<?php

namespace Moesif\MoesifBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('moesif');

        // Here you should define the structure of the configuration for your bundle
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('moesif_application_id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->booleanNode('debug')
                    ->defaultFalse()
                ->end()
                ->scalarNode('user_hooks_class')
                    ->defaultValue('Moesif\MoesifBundle\Interfaces\MoesifDefaultHooks')
                ->end()
                ->arrayNode('options')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('max_batch_size')
                            ->defaultValue(10)
                        ->end()
                        ->integerNode('max_queue_size')
                            ->defaultValue(15)
                        ->end()
                        ->scalarNode('consumer')
                            ->defaultValue('curl')
                        ->end()
                        ->scalarNode('host')
                            ->defaultValue('api.moesif.net')
                        ->end()
                        ->booleanNode('use_ssl')
                            ->defaultTrue()
                        ->end()
                        ->variableNode('error_callback')
                            ->defaultValue(null)
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
