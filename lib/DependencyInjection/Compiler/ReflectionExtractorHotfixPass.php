<?php namespace Vankosoft\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\PropertyInfo\PropertyReadInfoExtractorInterface;

use Vankosoft\ApiBundle\PropertyInfo\Extractor\ReflectionExtractor;

/**
 * @internal
 *
 * @see ReflectionExtractor
 */
final class ReflectionExtractorHotfixPass implements CompilerPassInterface
{
    public function process( ContainerBuilder $container ): void
    {
        /** @psalm-suppress UndefinedClass */
        if ( ! interface_exists( PropertyReadInfoExtractorInterface::class ) ) {
            // This class was introduced in Symfony 5.1, same Symfony version that introduced the BC break.
            return;
        }

        try {
            /** @psalm-suppress MissingDependency */
            $container->findDefinition( 'property_info.reflection_extractor' )->setClass( ReflectionExtractor::class );
        } catch ( ServiceNotFoundException $exception ) {
            return;
        }
    }
}
