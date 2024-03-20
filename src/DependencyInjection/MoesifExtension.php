<?php

namespace Moesif\MoesifBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\Definition\Processor;

class MoesifExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Load service definitions
        $configDir = __DIR__.'/../Resources/config';
        $loader = new YamlFileLoader($container, new FileLocator($configDir));
        $loader->load('services.yaml');

        // Create a new Configuration instance and process the configuration files
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);

        // Set parameters in the container from the configuration
        if (isset($config['moesif_application_id'])) {
            $container->setParameter('moesif.moesif_application_id', $config['moesif_application_id']);
        }

        if (isset($config['debug'])) {
            $container->setParameter('moesif.debug', $config['debug']);
        }

        if (isset($config['options'])) {
            $container->setParameter('moesif.options', $config['options']);
        }
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the prefix used when configuring the bundle.
     *
     * @return string The alias
     */
    public function getAlias(): string
    {
        // The alias should be the underscored version of the bundle name
        return 'moesif';
    }

}
