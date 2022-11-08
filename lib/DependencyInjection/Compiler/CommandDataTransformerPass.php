<?php namespace Vankosoft\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Vankosoft\ApiBundle\DataTransformer\CommandAwareInputDataTransformer;

/** @experimental */
final class CommandDataTransformerPass implements CompilerPassInterface
{
    public function process( ContainerBuilder $container )
    {
        $commandDataTransformersChainDefinition = new Definition( CommandAwareInputDataTransformer::class );

        $taggedServices                         = $container->findTaggedServiceIds( 'sylius.api.command_data_transformer' );

        foreach ( $taggedServices as $key => $value ) {
            $commandDataTransformersChainDefinition->addArgument( new Reference( $key ) );
        }

        $commandDataTransformersChainDefinition->addTag( 'api_platform.data_transformer' );

        $container->setDefinition(
            'sylius.api.data_transformer.command_aware_input_data_transformer',
            $commandDataTransformersChainDefinition
        );
    }
}
