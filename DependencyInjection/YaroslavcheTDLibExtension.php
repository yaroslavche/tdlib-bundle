<?php

namespace Yaroslavche\TDLibBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Yaroslavche\TDLibBundle\Service\TDLibService;

class YaroslavcheTDLibExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!isset($bundles['FrameworkBundle'])) {
            throw new \RuntimeException('FrameworkBundle must be installed to use YaroslavcheTDLibBundle.');
        }
        $container->prependExtensionConfig('tdlib', [
            'parameters' => [],
            'client' => []
        ]);
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $tdlibServiceDefinition = new Definition(TDLibService::class, [
            'parameters',
            'client'
        ]);
        $tdlibServiceDefinition->setArguments([
            $processedConfig['parameters'],
            $processedConfig['client'],
        ]);
        $container->setDefinition('tdlib_service', $tdlibServiceDefinition);

//        $container->setParameter('yaroslavche_tdlib.parameters', $processedConfig['parameters']);
    }
}
