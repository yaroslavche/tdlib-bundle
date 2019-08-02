<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class YaroslavcheTDLibExtension extends Extension
{
    const EXTENSION_ALIAS = 'yaroslavche_tdlib';

    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
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
