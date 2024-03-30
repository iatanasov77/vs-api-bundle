<?php namespace Vankosoft\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Vankosoft\ApiBundle\Model\RefreshToken;
use Vankosoft\ApiBundle\Repository\RefreshTokenRepository;

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
                ->scalarNode( 'description' )
                    ->defaultValue( 'API for VankoSoft FrontEnd Projects.' )->cannotBeEmpty()
                ->end()
                ->scalarNode( 'version' )
                    ->defaultValue( '0.0.1' )->cannotBeEmpty()
                ->end()
                
                ->scalarNode( 'orm_driver' )
                    ->defaultValue( SyliusResourceBundle::DRIVER_DOCTRINE_ORM )->cannotBeEmpty()
                ->end()
            ->end()
        ;
        
        $this->addResourcesSection( $rootNode );

        return $treeBuilder;
    }
    
    private function addResourcesSection( ArrayNodeDefinition $node ): void
    {
        $node
            ->children()
                ->arrayNode( 'resources' )
                    ->addDefaultsIfNotSet()
                    ->children()
        
                        ->arrayNode( 'refresh_token' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( RefreshToken::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( RefreshTokenRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
        
                    ->end()
                ->end()
            ->end()
        ;
    }
}
