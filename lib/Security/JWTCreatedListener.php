<?php namespace Vankosoft\ApiBundle\Security;

use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

/**
 * Manual: https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/2-data-customization.html#using-events-jwt-created
 */
class JWTCreatedListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    
    /**
     * @param RequestStack $requestStack
     */
    public function __construct( RequestStack $requestStack )
    {
        $this->requestStack = $requestStack;
    }
    
    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated( JWTCreatedEvent $event )
    {
        $request        = $this->requestStack->getCurrentRequest();
    
        $payload        = $event->getData();
        $payload['ip']  = $request->getClientIp();
    
        $event->setData( $payload );
    
        $header        = $event->getHeader();
        $header['cty'] = 'JWT';
    
        $event->setHeader( $header );
        
        // Override token expiration date calculation to be more flexible
        /*
        $expiration = new \DateTime( '+1 day' );
        $expiration->setTime( 2, 0, 0 );
        
        $payload        = $event->getData();
        $payload['exp'] = $expiration->getTimestamp();
        
        $event->setData( $payload );
        */
    }
}