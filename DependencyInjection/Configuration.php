<?php

namespace Yaroslavche\TDLibBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tdlib');
        $rootNode
            ->children()
                ->arrayNode('parameters')
                    ->children()
                        ->scalarNode('use_test_dc')->defaultFalse()->end()
                        ->scalarNode('database_directory')->defaultValue('/var/tmp/tdlib')->end()
                        ->scalarNode('files_directory')->defaultValue('/var/tmp/tdlib')->end()
                        ->scalarNode('use_file_database')->defaultFalse()->end()
                        ->scalarNode('use_chat_info_database')->defaultFalse()->end()
                        ->scalarNode('use_message_database')->defaultFalse()->end()
                        ->scalarNode('use_secret_chats')->defaultFalse()->end()
                        ->scalarNode('api_id')->isRequired()->end()
                        ->scalarNode('api_hash')->isRequired()->end()
                        ->scalarNode('system_language_code')->defaultValue('en')->end()
                        ->scalarNode('device_model')->defaultValue(php_uname('s'))->end()
                        ->scalarNode('system_version')->defaultValue(php_uname('v'))->end()
                        ->scalarNode('application_version')->defaultValue('0.0.8')->end()
                        ->scalarNode('enable_storage_optimizer')->defaultTrue()->end()
                        ->scalarNode('ignore_file_names')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('client')
                    ->children()
                        ->scalarNode('encryption_key')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
