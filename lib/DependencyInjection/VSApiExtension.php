<?php namespace Vankosoft\ApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/** @experimental */
final class VSApiExtension extends Extension implements PrependExtensionInterface
{
    use PrependApiPlatformTrait;
    
    public function load( array $config, ContainerBuilder $container ): void
    {
        $config = $this->processConfiguration( $this->getConfiguration( [], $container ), $config );
        
        $yamlLoader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );
        $xmlLoader  = new Loader\XmlFileLoader( $container, new FileLocator( __DIR__ . '/../Resources/config' ) );

        $container->setParameter( 'vs_api.enabled', $config['enabled'] );

        //$xmlLoader->load( 'services.xml' );
        $yamlLoader->load( 'services.yaml' );

        if ( $container->hasParameter( 'api_platform.enable_swagger_ui' ) && $container->getParameter( 'api_platform.enable_swagger_ui' ) ) {
            $xmlLoader->load( 'integrations/swagger.xml' );
        }
        
        $this->prepend( $container );
    }
    
    public function prepend( ContainerBuilder $container ): void
    {
        $config = $container->getExtensionConfig( $this->getAlias() );
        $config = $this->processConfiguration( $this->getConfiguration( [], $container ), $config );
        
        $this->prependApiPlatform( $container );
    }
}
