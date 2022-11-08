<?php namespace Vankosoft\ApiBundle\PropertyInfo\Extractor;

use Symfony\Component\PropertyInfo\PropertyListExtractorInterface;

/** @experimental */
final class EmptyPropertyListExtractor implements PropertyListExtractorInterface
{
    public function getProperties( $class, array $context = [] ): ?array
    {
        if ( class_exists( $class ) ) {
            return [];
        }

        return null;
    }
}
