<?php

namespace Yaroslavche\TDLibBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class YaroslavcheTDLibExtension extends Extension implements PrependExtensionInterface
{
    const EXTENSION_ALIAS = 'yaroslavche_tdlib';

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
//        $bundles = $container->getParameter('kernel.bundles');
//        if (!isset($bundles['FrameworkBundle'])) {
//            throw new RuntimeException('FrameworkBundle must be installed to use YaroslavcheTDLibBundle.');
//        }
        $container->prependExtensionConfig(static::EXTENSION_ALIAS, [
            'parameters' => [],
            'client' => []
        ]);
    }

    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception When loading config failed
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->prepend($container);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('yaroslavche_tdlib.service.tdlib');
        $definition->setArguments([
            $config['parameters'],
            $config['client'],
        ]);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return static::EXTENSION_ALIAS;
    }
}
