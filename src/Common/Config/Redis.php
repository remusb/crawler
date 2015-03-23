<?php
namespace Common\Config;
 
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
 
class Redis implements ConfigurationInterface {
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('redis');
 
        $rootNode
            ->children()
                ->scalarNode('socket')
                ->end()
                ->scalarNode('host')
                ->end()
                ->scalarNode('port')
                ->end()
            ->end()
        ;
 
        return $treeBuilder;
    }
}