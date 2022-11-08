<?php namespace Vankosoft\ApiBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Vankosoft\ApiBundle\DependencyInjection\Compiler\CommandDataTransformerPass;
use Vankosoft\ApiBundle\DependencyInjection\Compiler\ReflectionExtractorHotfixPass;

class VSApiBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build( ContainerBuilder $container ): void
    {
        parent::build( $container );
        
        $container->addCompilerPass( new CommandDataTransformerPass() );
        $container->addCompilerPass( new ReflectionExtractorHotfixPass() );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new \Vankosoft\ApiBundle\DependencyInjection\VSApiExtension();
    }
}
