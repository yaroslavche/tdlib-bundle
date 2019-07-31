<?php

namespace Yaroslavche\TDLibBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(YaroslavcheTDLibExtension::EXTENSION_ALIAS);
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('parameters')
                    ->children()
                        ->scalarNode('use_test_dc')->defaultFalse()->end()
                        ->scalarNode('database_directory')->defaultValue('/var/tmp/tdlib')->end()
                        ->scalarNode('files_directory')->defaultValue('/var/tmp/tdlib')->end()
                        ->booleanNode('use_file_database')->defaultFalse()->end()
                        ->booleanNode('use_chat_info_database')->defaultFalse()->end()
                        ->booleanNode('use_message_database')->defaultFalse()->end()
                        ->booleanNode('use_secret_chats')->defaultFalse()->end()
                        ->integerNode('api_id')->isRequired()->end()
                        ->scalarNode('api_hash')->isRequired()->end()
                        ->scalarNode('system_language_code')->defaultValue('en')->end()
                        ->scalarNode('device_model')->defaultValue(php_uname('s'))->end()
                        ->scalarNode('system_version')->defaultValue(php_uname('v'))->end()
                        ->scalarNode('application_version')->defaultValue('0.0.8')->end()
                        ->booleanNode('enable_storage_optimizer')->defaultTrue()->end()
                        ->booleanNode('ignore_file_names')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('client')
                    ->children()
                        ->scalarNode('encryption_key')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
