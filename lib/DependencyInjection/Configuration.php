<?php namespace Vankosoft\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/** @experimental */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder( 'vs_api' );

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode( 'enabled' )
                    ->defaultFalse()
                ->end()
                ->scalarNode( 'title' )
                    ->defaultValue( 'VankoSoft API' )->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
