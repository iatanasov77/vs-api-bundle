<?php namespace Vankosoft\ApiBundle\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Vankosoft\ApiBundle\Command\CommandAwareDataTransformerInterface;

/** @experimental */
final class CommandAwareInputDataTransformer implements DataTransformerInterface
{
    /** @var CommandDataTransformerInterface[] */
    private array $commandDataTransformers;

    public function __construct( CommandDataTransformerInterface ...$commandDataTransformers )
    {
        $this->commandDataTransformers = $commandDataTransformers;
    }

    public function transform( $object, string $to, array $context = [] )
    {
        foreach ( $this->commandDataTransformers as $transformer ) {
            if ( $transformer->supportsTransformation( $object ) ) {
                $object = $transformer->transform( $object, $to, $context );
            }
        }

        return $object;
    }

    public function supportsTransformation( $data, string $to, array $context = [] ): bool
    {
        return
            isset( $context['input']['class'] ) &&
            is_a( $context['input']['class'], CommandAwareDataTransformerInterface::class, true )
        ;
    }
}
