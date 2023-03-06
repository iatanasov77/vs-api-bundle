<?php namespace Vankosoft\ApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait PrependApiPlatformTrait
{
    private function prependApiPlatform( ContainerBuilder $container ): void
    {
        if ( ! $container->hasExtension( 'api_platform' ) ) {
            return;
        }
        
        $vsApiConfig    = $container->getExtensionConfig( 'vs_api' );
        //echo "<pre>"; var_dump( $vsApiConfig ); die;
        
        $apiPlatformConfig        = $container->getExtensionConfig( 'api_platform' );
        $container->prependExtensionConfig( 'api_platform', [
            'title'  => $vsApiConfig['title']
        ]);

        //$this->debug( $container );
    }
    
    private function debug( ContainerBuilder $container )
    {
        echo '<pre>';
        //var_dump( $container->getParameter( 'vs_payment.model.gateway_config.class' ) );
        echo '<br><br><br><br>';
        var_dump( $container->getExtensionConfig( 'vs_api' ) );
        echo '<br><br><br><br>';
        var_dump( $container->getExtensionConfig( 'api_platform' ) ); die;
    }
}
