<?php
declare(strict_types=1);

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
                        /** required */
                        ->integerNode('api_id')->isRequired()->end()
                        ->scalarNode('api_hash')->isRequired()->end()
                        ->scalarNode('system_language_code')->isRequired()->defaultValue('en')->end()
                        ->scalarNode('device_model')->isRequired()->defaultValue(php_uname('s'))->end()
                        ->scalarNode('system_version')->isRequired()->defaultValue(php_uname('v'))->end()
                        ->scalarNode('application_version')->isRequired()->defaultValue('0.0.1')->end()
                        /** optional */
                        ->scalarNode('use_test_dc')->defaultTrue()->end()
                        ->scalarNode('database_directory')->defaultValue('/var/tmp/tdlib')->end()
                        ->scalarNode('files_directory')->defaultValue('/var/tmp/tdlib')->end()
                        ->booleanNode('use_file_database')->defaultTrue()->end()
                        ->booleanNode('use_chat_info_database')->defaultTrue()->end()
                        ->booleanNode('use_message_database')->defaultTrue()->end()
                        ->booleanNode('use_secret_chats')->defaultTrue()->end()
                        ->booleanNode('enable_storage_optimizer')->defaultTrue()->end()
                        ->booleanNode('ignore_file_names')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('client')
                    ->children()
                        ->scalarNode('phone_number')->defaultNull()->end()
                        ->scalarNode('encryption_key')->defaultValue('')->end()
                        ->floatNode('default_timeout')->defaultValue(0.5)->end()
                        ->booleanNode('auto_init')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
