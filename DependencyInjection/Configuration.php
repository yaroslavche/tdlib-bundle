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
                ->scalarNode('api_id')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->defaultValue(11111)
                ->end()
                ->scalarNode('api_hash')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->defaultValue('abcdef1234567890abcdef1234567890')
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
